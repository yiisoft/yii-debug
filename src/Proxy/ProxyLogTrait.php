<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Yiisoft\Yii\Debug\Event\ProxyMethodCallEvent;

trait ProxyLogTrait
{
    private ContainerProxyConfig $config;

    protected function log(string $service, object $instance, string $method, array $arguments, $result, float $timeStart): void
    {
        $error = $this->getCurrentError();
        $this->processLogData($arguments, $result, $error);

        if ($this->config->getCollector() !== null) {
            $this->logToCollector($service, $instance, $method, $arguments, $result, $error, $timeStart);
        }

        if ($this->config->getDispatcher() !== null) {
            $this->logToEvent($service, $instance, $method, $arguments, $result, $error, $timeStart);
        }
    }

    private function processLogData(array &$arguments, &$result, ?object &$error): void
    {
        if (!($this->config->getLogLevel() & ContainerInterfaceProxy::LOG_ARGUMENTS)) {
            $arguments = null;
        }

        if (!($this->config->getLogLevel() & ContainerInterfaceProxy::LOG_RESULT)) {
            $result = null;
        }

        if (!($this->config->getLogLevel() & ContainerInterfaceProxy::LOG_ERROR)) {
            $error = null;
        }
    }

    private function logToCollector(string $service, object $instance, string $method, ?array $arguments, $result, ?object $error, float $timeStart): void
    {
        $this->config->getCollector()->collect(
            $service,
            get_class($instance),
            $method,
            $arguments,
            $result,
            $this->getCurrentResultStatus(),
            $error,
            $timeStart,
            microtime(true),
        );
    }

    private function logToEvent(string $service, object $instance, string $method, ?array $arguments, $result, ?object $error, float $timeStart): void
    {
        $this->config->getDispatcher()->dispatch(new ProxyMethodCallEvent(
            $service,
            get_class($instance),
            $method,
            $arguments,
            $result,
            $this->getCurrentResultStatus(),
            $error,
            $timeStart,
            microtime(true),
        ));
    }
}
