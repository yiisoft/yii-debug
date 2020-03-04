<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;

final class ContainerProxy extends ContainerInterfaceProxy
{
    public function __construct(
        ContainerInterface $container,
        ContainerProxyConfig $config
    ) {
        $container instanceof Container ? $container->delegateLookup($this) : null;
        parent::__construct($container, $config);
    }

    public function delegateLookup(ContainerInterface $container): void
    {
        $this->container->delegateLookup($container);
    }

    private function checkNativeContainer(): void
    {
        if (!$this->container instanceof Container) {
            throw new \RuntimeException('This method is for Yiisoft\Di\Container only');
        }
    }
}
