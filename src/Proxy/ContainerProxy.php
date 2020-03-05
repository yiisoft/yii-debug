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
}
