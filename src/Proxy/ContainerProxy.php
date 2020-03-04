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
        $container = $container instanceof Container ? $container->delegateLookup($this) : $container;
        parent::__construct($container, $config);
    }

    public function delegateLookup(ContainerInterface $container): ContainerInterface
    {
        $this->checkNativeContainer();
        $newContainer = clone $this;
        $newContainer->container = $this->container->delegateLookup($container);

        return $newContainer;
    }

    private function checkNativeContainer(): void
    {
        if (!$this->container instanceof Container) {
            throw new \RuntimeException('This method is for Yiisoft\Di\Container only');
        }
    }
}
