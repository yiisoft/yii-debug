<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yiisoft\Di\Container;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Proxy\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
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

    public function testGetAndHas(): void
    {
        $containerProxy = new ContainerInterfaceProxy($this->getContainer(), $this->getConfig());

        $this->assertTrue($containerProxy->isActive());
        $this->assertTrue($containerProxy->has(LoggerInterface::class));
        $this->assertInstanceOf(LoggerInterfaceProxy::class, $containerProxy->get(LoggerInterface::class));
    }

    public function testGetAndHasWithWrongId(): void
    {
        $this->expectException(ContainerExceptionInterface::class);
        $this->expectExceptionMessage(sprintf("No definition for %s", EventDispatcherInterface::class));

        $containerProxy = new ContainerInterfaceProxy($this->getContainer(), $this->getConfig());

        $containerProxy->has(EventDispatcherInterface::class);
        $containerProxy->get(EventDispatcherInterface::class);
    }

    public function testGetAndHasWithNotService(): void
    {
        $container = new Container(
            [
                EventDispatcherInterface::class => Dispatcher::class,
                ListenerProviderInterface::class => Provider::class,
                LoggerInterface::class => NullLogger::class,
                LogCollectorInterface::class => LogCollector::class,
            ]
        );

        $containerProxy = new ContainerInterfaceProxy($container, $this->getConfig());

        $this->assertTrue($containerProxy->has(EventDispatcherInterface::class));
        $this->assertNotInstanceOf(EventDispatcherInterfaceProxy::class, $containerProxy->get(EventDispatcherInterface::class));
    }

    private function getConfig(): ContainerProxyConfig
    {
        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        return new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
            ],
            $dispatcherMock,
            new ServiceCollector(),
            '@tests/runtime',
            1
        );
    }

    private function getContainer(): Container
    {
        return new Container(
            [
                LoggerInterface::class => NullLogger::class,
                LogCollectorInterface::class => LogCollector::class,
            ]
        );
    }
}
