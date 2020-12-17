<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;
use Yiisoft\Yii\Debug\Proxy\ServiceMethodProxy;
use Yiisoft\Yii\Debug\Proxy\ServiceProxy;

final class ServiceProxyTest extends TestCase
{
    public function testServiceProxy(): void
    {
        $object = new ServiceProxy('test', new \stdClass(), new ContainerProxyConfig());
        $this->assertIsObject($object);
    }

    public function testServiceMethodProxy(): void
    {
        $object = new ServiceMethodProxy('test', new \stdClass(), ['__toString'], new ContainerProxyConfig());
        $this->assertIsObject($object);
    }
}
