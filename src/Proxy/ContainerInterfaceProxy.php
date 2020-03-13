<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Yiisoft\Container\Proxy\ContainerProxyInterface;
use Yiisoft\Proxy\ProxyManager;
use Yiisoft\Proxy\ProxyTrait;

class ContainerInterfaceProxy implements ContainerProxyInterface
{
    use ProxyTrait;
    use ProxyLogTrait;

    public const LOG_ARGUMENTS = 1;

    public const LOG_RESULT = 2;

    public const LOG_ERROR = 4;

    protected ContainerInterface $container;

    private ProxyManager $proxyManager;

    private array $decoratedServices = [];

    private array $serviceProxy = [];

    public function __construct(ContainerInterface $container, ContainerProxyConfig $config)
    {
        $this->config = $config;
        $this->container = $container;
        $this->proxyManager = new ProxyManager($this->config->getProxyCachePath());
    }

    public function withDecoratedServices(array $decoratedServices): ContainerProxyInterface
    {
        $proxy = clone $this;
        $this->config = $this->config->withDecoratedServices($decoratedServices);

        return $proxy;
    }

    public function isActive(): bool
    {
        return $this->config->getIsActive() && $this->config->getDecoratedServices() !== [];
    }

    public function get($id, array $params = [])
    {
        $this->resetCurrentError();
        $timeStart = microtime(true);
        try {
            $instance = null;
            $instance = $this->getInstance($id, $params);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->log(ContainerInterface::class, $this->container, 'get', [$id, $params], $instance, $timeStart);
        }

        if (is_object($instance) && $this->isDecorated($id) && (($proxy = $this->getServiceProxyCache($id)) || ($proxy = $this->getServiceProxy($id, $instance)))) {
            $this->setServiceProxyCache($id, $proxy);
            return $proxy;
        }

        return $instance;
    }

    public function has($id): bool
    {
        $this->resetCurrentError();
        $timeStart = microtime(true);
        try {
            $result = null;
            $result = $this->container->has($id);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->log(ContainerInterface::class, $this->container, 'has', [$id], $result, $timeStart);
        }

        return $result;
    }

    private function isDecorated(string $service): bool
    {
        return $this->isActive() && $this->config->hasDecoratedService($service);
    }

    private function getServiceProxy(string $service, object $instance): ?object
    {
        if (!$this->isDecorated($service)) {
            return null;
        }

        if ($this->config->hasDecoratedServiceCallableConfig($service)) {
            return $this->getServiceProxyFromCallable($this->config->getDecoratedServiceConfig($service));
        }

        if ($this->config->hasDecoratedServiceArrayConfigWithStringKeys($service)) {
            return $this->getCommonMethodProxy($service, $instance, $this->config->getDecoratedServiceConfig($service));
        }

        if ($this->config->hasDecoratedServiceArrayConfig($service)) {
            return $this->getServiceProxyFromArray($instance, $this->config->getDecoratedServiceConfig($service));
        }

        if (interface_exists($service) && ($this->config->hasCollector() || $this->config->hasDispatcher())) {
            return $this->getCommonServiceProxy($service, $instance);
        }

        return null;
    }

    private function getCommonMethodProxy(string $service, object $instance, array $callbacks): ?object
    {
        $methods = [];
        while ($callback = current($callbacks)) {
            $method = key($callbacks);
            if (is_string($method) && is_callable($callback)) {
                $methods[$method] = $callback;
            }
            next($callbacks);
        }

        return $this->proxyManager->createObjectProxyFromInterface(
            $service,
            ServiceMethodProxy::class,
            [$service, $instance, $methods, $this->config]
        );
    }

    private function getServiceProxyFromCallable(callable $callback): ?object
    {
        return $callback($this->container);
    }

    private function getServiceProxyFromArray(object $instance, array $params): ?object
    {
        try {
            $proxyClass = array_shift($params);
            foreach ($params as $index => $param) {
                if (is_string($param)) {
                    try {
                        $params[$index] = $this->container->get($param);
                    } catch (\Exception $e) {
                        //leave as is
                    }
                }
            }
            return new $proxyClass($instance, ...$params);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getCommonServiceProxy(string $service, object $instance): object
    {
        return $this->proxyManager->createObjectProxyFromInterface(
            $service,
            ServiceProxy::class,
            [$service, $instance, $this->config]
        );
    }

    private function getInstance(string $id, array $params)
    {
        if ($params === []) {
            return $this->container->get($id);
        }

        return $this->container->get($id, $params);
    }

    private function getServiceProxyCache(string $service): ?object
    {
        return $this->serviceProxy[$service] ?? null;
    }

    private function setServiceProxyCache(string $service, object $instance): void
    {
        $this->serviceProxy[$service] = $instance;
    }
}
