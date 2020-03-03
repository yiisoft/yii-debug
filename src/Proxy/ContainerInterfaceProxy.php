<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Di\ContainerProxyInterface;
use Yiisoft\Proxy\ProxyManager;
use Yiisoft\Yii\Debug\Collector\CommonServiceCollectorInterface;
use Yiisoft\Yii\Debug\Event\ProxyMethodCallEvent;

class ContainerInterfaceProxy implements ContainerProxyInterface
{
    const LOG_ARGUMENTS = 1 << 0;

    const LOG_RESULT = 1 << 1;

    const LOG_ERROR = 1 << 2;

    protected ContainerInterface $container;

    private int $logLevel = 0;

    private array $decoratedServices = [];

    private bool $active = false;

    private ?EventDispatcherInterface $dispatcher = null;

    private ?CommonServiceCollectorInterface $commonCollector = null;

    private array $serviceProxy = [];

    private ?object $currentError = null;

    private ?string $proxyCachePath = null;

    private ProxyManager $proxyManager;

    public function __construct(
        ContainerInterface $container,
        ContainerProxyConfig $config
    ) {
        $this->container = $container;
        $this->active = $config->getIsActive();
        $this->decoratedServices = $config->getDecoratedServices();
        $this->dispatcher = $config->getDispatcher();
        $this->commonCollector = $config->getCollector();
        $this->proxyCachePath = $config->getProxyCachePath();
        $this->logLevel = $config->getLogLevel();
        $this->proxyManager = new ProxyManager($this->proxyCachePath);
    }

    public function withDecoratedServices(array $decoratedServices): ContainerProxyInterface
    {
        $proxy = clone $this;
        $proxy->decoratedServices = array_merge($this->decoratedServices, $decoratedServices);

        return $proxy;
    }

    public function isActive(): bool
    {
        return $this->active && $this->decoratedServices !== [];
    }

    public function get($id, array $params = [])
    {
        $this->resetCurrentError();
        try {
            $instance = null;
            $timeStart = microtime(true);
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
        try {
            $result = null;
            $timeStart = microtime(true);
            $result = $this->container->has($id);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->log('has', [$id], $result, $timeStart);
            return $result;
        }
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

        if ($this->commonCollector !== null) {
            $this->logToCollector($method, $arguments, $result, $error, $timeStart);
        }

        if ($this->dispatcher !== null) {
            $this->logToEvent($method, $arguments, $result, $error, $timeStart);
        }
    }

    private function processLogData(array &$arguments, &$result, ?object &$error): void
    {
        if (!($this->logLevel & self::LOG_ARGUMENTS)) {
            $arguments = null;
        }

        if (!($this->logLevel & self::LOG_RESULT)) {
            $result = null;
        }

        if (!($this->logLevel & self::LOG_ERROR)) {
            $error = null;
        }
    }

    private function logToCollector(string $method, ?array $arguments, $result, ?object $error, float $timeStart): void
    {
        $this->commonCollector->collect(
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
        $this->dispatcher->dispatch(new ProxyMethodCallEvent(
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
        return isset($this->decoratedServices[$service]) || in_array($service, $this->decoratedServices, true);
    }

    private function getServiceProxy(string $service, object $instance): ?object
    {
        if (!$this->isDecorated($service)) {
            return null;
        }

        if (isset($this->decoratedServices[$service]) && is_callable($this->decoratedServices[$service])) {
            return $this->getServiceProxyFromCallable($service, $instance);
        } elseif (isset($this->decoratedServices[$service]) && is_array($this->decoratedServices[$service]) &&
            !isset($this->decoratedServices[$service][0])) {
            return $this->getCommonMethodProxy($service, $instance, $this->decoratedServices[$service]);
        } elseif (isset($this->decoratedServices[$service]) && is_array($this->decoratedServices[$service])) {
            return $this->getServiceProxyFromArray($service, $instance);
        } elseif (interface_exists($service) && ($this->commonCollector !== null || $this->dispatcher !== null)) {
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
            [$service, $instance, $methods, $this->commonCollector, $this->dispatcher, $this->logLevel]
        );
    }

    private function getServiceProxyFromCallable(string $service, object $instance): ?object
    {
        return $this->decoratedServices[$service]($this->container);
    }

    private function getServiceProxyFromArray(string $service, object $instance): ?object
    {
        try {
            $params = $this->decoratedServices[$service];
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
            [$service, $instance, $this->commonCollector, $this->dispatcher, $this->logLevel]
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
