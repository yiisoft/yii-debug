<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;

final class ContainerProxy extends ContainerInterfaceProxy
{
    public function __construct(ContainerInterface $container, ContainerProxyConfig $config)
    {
        if ($container instanceof Container) {
            $delegator = new ContainerDelegator($container);
            $delegator->delegateLookup($this);
        }

        parent::__construct($container, $config);
    }
}
