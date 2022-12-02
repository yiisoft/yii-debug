<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Yiisoft\Proxy\ProxyManager;
use Yiisoft\Proxy\ProxyTrait;

use function is_callable;
use function is_object;
use function is_string;

class ContainerInterfaceProxy implements ContainerInterface
{
    use ProxyLogTrait;
    use ProxyTrait;

    public const LOG_ARGUMENTS = 1;

    public const LOG_RESULT = 2;

    public const LOG_ERROR = 4;

    private ProxyManager $proxyManager;

    private array $decoratedServices = [];

    private array $serviceProxy = [];

    public function __construct(protected ContainerInterface $container, ContainerProxyConfig $config)
    {
        $this->config = $config;
        $this->proxyManager = new ProxyManager($this->config->getProxyCachePath());
    }

    public function withDecoratedServices(array $decoratedServices): self
    {
        $new = clone $this;
        $new->config = $this->config->withDecoratedServices($decoratedServices);
        return $new;
    }

    /**
     * @psalm-suppress InvalidCatch
     */
    public function get($id)
    {
        $this->resetCurrentError();
        $timeStart = microtime(true);
        try {
            $instance = null;
            $instance = $this->getInstance($id);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->logProxy(ContainerInterface::class, $this->container, 'get', [$id], $instance, $timeStart);
        }

        if (
            is_object($instance)
            && (($proxy = $this->getServiceProxyCache($id)) || ($proxy = $this->getServiceProxy($id, $instance)))
        ) {
            $this->setServiceProxyCache($id, $proxy);
            return $proxy;
        }

        return $instance;
    }

    private function getInstance(string $id)
    {
        if ($id === ContainerInterface::class) {
            return $this;
        }

        return $this->container->get($id);
    }

    private function isDecorated(string $service): bool
    {
        return $this->isActive() && $this->config->hasDecoratedService($service);
    }

    public function isActive(): bool
    {
        return $this->config->getIsActive() && $this->config->getDecoratedServices() !== [];
    }

    private function getServiceProxyCache(string $service): ?object
    {
        return $this->serviceProxy[$service] ?? null;
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

    private function getServiceProxyFromCallable(callable $callback): ?object
    {
        return $callback($this);
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

        return $this->proxyManager->createObjectProxy(
            $service,
            ServiceMethodProxy::class,
            [$service, $instance, $methods, $this->config]
        );
    }

    private function getServiceProxyFromArray(object $instance, array $params): ?object
    {
        try {
            $proxyClass = array_shift($params);
            foreach ($params as $index => $param) {
                if (is_string($param)) {
                    try {
                        $params[$index] = $this->get($param);
                    } catch (Exception) {
                        //leave as is
                    }
                }
            }
            return new $proxyClass($instance, ...$params);
        } catch (Exception) {
            return null;
        }
    }

    private function getCommonServiceProxy(string $service, object $instance): object
    {
        return $this->proxyManager->createObjectProxy(
            $service,
            ServiceProxy::class,
            [$service, $instance, $this->config]
        );
    }

    private function setServiceProxyCache(string $service, object $instance): void
    {
        $this->serviceProxy[$service] = $instance;
    }

    /**
     * @psalm-suppress InvalidCatch
     */
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
            $this->logProxy(ContainerInterface::class, $this->container, 'has', [$id], $result, $timeStart);
        }

        return $result;
    }
}
