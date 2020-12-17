<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;

final class ContainerProxyTest extends TestCase
{
    public function testContainerProxy(): void
    {
        $object = new ContainerProxy(new Container(), new ContainerProxyConfig());
        $this->assertIsObject($object);
        $this->assertFalse($object->isActive());
    }

    public function testGet(): void
    {
        $container = new ContainerProxy(new Container(), new ContainerProxyConfig(true));
        $this->assertIsObject($container->get(ContainerInterface::class));
    }
}
