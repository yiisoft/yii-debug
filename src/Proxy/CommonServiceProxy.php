<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Proxy\ObjectProxy;
use Yiisoft\Yii\Debug\Collector\CommonServiceCollectorInterface;
use Yiisoft\Yii\Debug\Event\ProxyMethodCallEvent;

class CommonServiceProxy extends ObjectProxy
{
    private string $service;

    private int $logLevel = 0;

    private ?CommonServiceCollectorInterface $collector = null;

    private ?EventDispatcherInterface $dispatcher = null;

    public function __construct(
        string $service,
        object $instance,
        CommonServiceCollectorInterface $collector = null,
        EventDispatcherInterface $dispatcher = null,
        int $logLevel = 0
    ) {
        $this->service = $service;
        $this->collector = $collector;
        $this->dispatcher = $dispatcher;
        $this->logLevel = $logLevel;
        parent::__construct($instance);
    }

    protected function executeMethodProxy(string $methodName, array $arguments, $result, float $timeStart)
    {
        $this->log($methodName, $arguments, $result, $timeStart);
        return $result;
    }

    protected function getNewStaticInstance(object $instance): ObjectProxy
    {
        return new static($this->service, $instance, $this->collector, $this->dispatcher, $this->logLevel);
    }

    protected function log(string $method, array $arguments, $result, float $timeStart): void
    {
        $error = $this->getCurrentError();
        $this->processLogData($arguments, $result, $error);

        if ($this->collector !== null) {
            $this->logToCollector($method, $arguments, $result, $error, $timeStart);
        }

        if ($this->dispatcher !== null) {
            $this->logToEvent($method, $arguments, $result, $error, $timeStart);
        }
    }

    private function processLogData(array &$arguments, &$result, ?object &$error): void
    {
        if (!($this->logLevel & ContainerInterfaceProxy::LOG_ARGUMENTS)) {
            $arguments = null;
        }

        if (!($this->logLevel & ContainerInterfaceProxy::LOG_RESULT)) {
            $result = null;
        }

        if (!($this->logLevel & ContainerInterfaceProxy::LOG_ERROR)) {
            $error = null;
        }
    }

    protected function getService(): string
    {
        return $this->service;
    }

    protected function getCollector(): ?CommonServiceCollectorInterface
    {
        return $this->collector;
    }

    protected function getDispatcher(): ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    protected function getLogLevel(): int
    {
        return $this->logLevel;
    }

    private function logToCollector(string $method, ?array $arguments, $result, ?object $error, float $timeStart): void
    {
        $this->collector->collect(
            $this->service,
            get_class($this->getInstance()),
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
                $this->service,
                get_class($this->getInstance()),
                $method,
                $arguments,
                $result,
                $this->getCurrentResultStatus(),
                $error,
                $timeStart,
                microtime(true),
                )
        );
    }
}