<?php

declare(strict_types=1);

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

    protected function executeMethodProxy(string $methodName, array $arguments, $result, float $timeStart)
    {
        try {
            if (isset($this->methods[$methodName])) {
                $callback = $this->methods[$methodName];
                $result = $callback($result, ...$arguments);
            }
        } finally {
            $this->logProxy($this->getService(), $this->getInstance(), $methodName, $arguments, $result, $timeStart);
        }

        return $result;
    }

    protected function getNewStaticInstance(object $instance): ObjectProxy
    {
        return new static($this->getService(), $instance, $this->methods, $this->getConfig());
    }
}
