<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Yiisoft\Proxy\ObjectProxy;

class ServiceMethodProxy extends ServiceProxy
{
    private array $methods;

    public function __construct(
        string $service,
        object $instance,
        array $methods,
        ContainerProxyConfig $config
    ) {
        $this->methods = $methods;
        parent::__construct($service, $instance, $config);
    }

    protected function executeMethodProxy(string $method, array $arguments, $result, float $timeStart)
    {
        try {
            if (isset($this->methods[$method])) {
                $callback = $this->methods[$method];
                $result = $callback($result, ...$arguments);
            }
        } finally {
            $this->log($this->getService(), $this->getInstance(), $method, $arguments, $result, $timeStart);
        }

        return $result;
    }

    protected function getNewStaticInstance(object $instance): ObjectProxy
    {
        return new static($this->getService(), $instance, $this->methods, $this->getConfig());
    }
}
