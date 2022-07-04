<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Proxy;

use Yiisoft\Proxy\ObjectProxy;

class ServiceProxy extends ObjectProxy
{
    use ProxyLogTrait;

    private string $service;

    public function __construct(
        string $service,
        object $instance,
        ContainerProxyConfig $config
    ) {
        $this->service = $service;
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
