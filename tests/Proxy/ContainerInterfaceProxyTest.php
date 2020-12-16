<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Proxy\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

class ContainerInterfaceProxyTest extends TestCase
{
    public function testImmutability(): void
    {
        $containerProxy = new ContainerInterfaceProxy(new Container(), new ContainerProxyConfig());

        $this->assertNotSame(
            $containerProxy,
            $containerProxy->withDecoratedServices(
                [
                    LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
                ]
            )
        );
    }

    public function testGetAndHas()
    {
        $container = new Container(
            [
                LoggerInterface::class => NullLogger::class,
                LogCollectorInterface::class => LogCollector::class,
            ]
        );
        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $config = new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
            ],
            $dispatcherMock,
            new ServiceCollector(),
            '@tests/runtime',
            1
        );

        $containerProxy = new ContainerInterfaceProxy($container, $config);

        $this->assertTrue($containerProxy->isActive());
        $this->assertTrue($containerProxy->has(LoggerInterface::class));
        $this->assertInstanceOf(LoggerInterfaceProxy::class, $containerProxy->get(LoggerInterface::class));
    }
}
