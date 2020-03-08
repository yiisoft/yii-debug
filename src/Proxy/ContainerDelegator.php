<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\AbstractContainerConfigurator;
use Yiisoft\Di\Container;

class ContainerDelegator extends AbstractContainerConfigurator
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function delegateLookup(ContainerInterface $container): void
    {
        $this->container->delegateLookup($container);
    }
}