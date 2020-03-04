<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Yiisoft\Di\ContainerProxyInterface;
use Yiisoft\Proxy\ProxyManager;
use Yiisoft\Yii\Debug\Event\ProxyMethodCallEvent;

class ContainerInterfaceProxy implements ContainerProxyInterface
{
    public const LOG_ARGUMENTS = 1;

    public const LOG_RESULT = 2;

    public const LOG_ERROR = 4;

    protected ContainerInterface $container;

    private array $decoratedServices = [];

    private ContainerProxyConfig $config;

    private array $serviceProxy = [];

    private ?object $currentError = null;

    private ProxyManager $proxyManager;

    public function __construct(
        ContainerInterface $container,
        ContainerProxyConfig $config
    ) {
        $this->config = $config;
        $this->container = $container;
        $this->proxyManager = new ProxyManager($this->config->getProxyCachePath());
    }

    public function withDecoratedServices(array $decoratedServices): ContainerProxyInterface
    {
        $proxy = clone $this;
        $proxy->decoratedServices = array_merge($this->config->getDecoratedServices(), $decoratedServices);

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
            $this->log('get', [$id, $params], $instance, $timeStart);
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
            $this->log('has', [$id], $result, $timeStart);
        }

        return $result;
    }

    protected function getCurrentResultStatus(): string
    {
        return $this->currentError === null ? 'success' : 'failed';
    }

    protected function repeatError(object $error): void
    {
        $this->currentError = $error;
        $errorClass = get_class($error);
        throw new $errorClass($error->getMessage());
    }

    protected function resetCurrentError(): void
    {
        $this->currentError = null;
    }

    protected function log(string $method, array $arguments, $result, float $timeStart): void
    {
        $error = $this->currentError;
        $this->processLogData($arguments, $result, $error);

        if ($this->config->getCollector() !== null) {
            $this->logToCollector($method, $arguments, $result, $error, $timeStart);
        }

        if ($this->config->getDispatcher() !== null) {
            $this->logToEvent($method, $arguments, $result, $error, $timeStart);
        }
    }

    private function processLogData(array &$arguments, &$result, ?object &$error): void
    {
        if (!($this->config->getLogLevel() & self::LOG_ARGUMENTS)) {
            $arguments = null;
        }

        if (!($this->config->getLogLevel() & self::LOG_RESULT)) {
            $result = null;
        }

        if (!($this->config->getLogLevel() & self::LOG_ERROR)) {
            $error = null;
        }
    }

    private function logToCollector(string $method, ?array $arguments, $result, ?object $error, float $timeStart): void
    {
        $this->config->getCollector()->collect(
            ContainerInterface::class,
            get_class($this->container),
            $method,
            $arguments,
            $result,
            $this->getCurrentResultStatus(),
            $error,
            $timeStart,
            microtime(true),
            );
    }

    private function logToEvent(string $method, ?array $arguments, $result, ?object $error, float $timeStart): void
    {
        $this->config->getDispatcher()->dispatch(new ProxyMethodCallEvent(
            ContainerInterface::class,
            get_class($this->container),
            $method,
            $arguments,
            $result,
            $this->getCurrentResultStatus(),
            $error,
            $timeStart,
            microtime(true),
            ));
    }

    private function isDecorated(string $service): bool
    {
        return isset($this->config->getDecoratedServices()[$service]) || in_array($service, $this->config->getDecoratedServices(), true);
    }

    private function getServiceProxy(string $service, object $instance): ?object
    {
        if (!$this->isDecorated($service)) {
            return null;
        }

        $decoratedServices = $this->config->getDecoratedServices();
        if (isset($decoratedServices[$service]) && is_callable($decoratedServices[$service])) {
            return $this->getServiceProxyFromCallable($service, $instance);
        } elseif (isset($decoratedServices[$service]) && is_array($decoratedServices[$service]) &&
            !isset($decoratedServices[$service][0])) {
            return $this->getCommonMethodProxy($service, $instance, $decoratedServices[$service]);
        } elseif (isset($decoratedServices[$service]) && is_array($decoratedServices[$service])) {
            return $this->getServiceProxyFromArray($service, $instance);
        } elseif (interface_exists($service) && ($this->config->getCollector() !== null || $this->config->getDispatcher() !== null)) {
            return $this->getCommonServiceProxy($service, $instance);
        }

        return null;
    }

    private function getCommonMethodProxy(string $service, object $instance, array $callbacks): ?object
    {
        $methods = [];
        while ($callback = current($callbacks)) {
            $method = key($callbacks);
            next($callbacks);
            if (is_string($method) && is_callable($callback)) {
                $methods[$method] = $callback;
            }
        }

        return $this->proxyManager->createObjectProxyFromInterface(
            $service,
            CommonMethodProxy::class,
            [$service, $instance, $methods, $this->config->getCollector(), $this->config->getDispatcher(), $this->config->getLogLevel()]
        );
    }

    private function getServiceProxyFromCallable(string $service, object $instance): ?object
    {
        return $this->config->getDecoratedServices()[$service]($this->container);
    }

    private function getServiceProxyFromArray(string $service, object $instance): ?object
    {
        try {
            $params = $this->config->getDecoratedServices()[$service];
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
            CommonServiceProxy::class,
            [$service, $instance, $this->config->getCollector(), $this->config->getDispatcher(), $this->config->getLogLevel()]
        );
    }

    private function getInstance(string $id, array $params)
    {
        if ($params === []) {
            return $instance = $this->container->get($id);
        }

        return $instance = $this->container->get($id, $params);
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
