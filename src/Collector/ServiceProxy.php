<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Proxy\ObjectProxy;

class ServiceProxy extends ObjectProxy
{
    use ProxyLogTrait;

    public function __construct(
        private string $service,
        object $instance,
        ContainerProxyConfig $config,
    ) {
        $this->config = $config;
        parent::__construct($instance);
    }

    protected function afterCall(string $methodName, array $arguments, mixed $result, float $timeStart): mixed
    {
        $this->logProxy($this->service, $this->getInstance(), $methodName, $arguments, $result, $timeStart);
        return $result;
    }

    protected function getNewStaticInstance(object $instance): ObjectProxy
    {
        return new static($this->service, $instance, $this->config);
    }

    protected function getService(): string
    {
        return $this->service;
    }

    protected function getConfig(): ContainerProxyConfig
    {
        return $this->config;
    }
}
