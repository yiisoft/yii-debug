<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

final class ContainerProxyConfigTest extends TestCase
{
    public function testImmutability(): void
    {
        $config = new ContainerProxyConfig();

        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->assertNotSame($config, $config->activate());
        $this->assertNotSame($config, $config->withCollector(new ServiceCollector()));
        $this->assertNotSame($config, $config->withLogLevel(1));
        $this->assertNotSame($config, $config->withProxyCachePath('@tests/runtime'));
        $this->assertNotSame(
            $config,
            $config->withDispatcher(
                new EventDispatcherInterfaceProxy($dispatcherMock, new EventCollector())
            )
        );
        $this->assertNotSame(
            $config,
            $config->withDecoratedServices(
                [
                    LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
                ]
            )
        );
    }

    public function testGetters(): void
    {
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

        $this->assertTrue($config->getIsActive());
        $this->assertInstanceOf(EventDispatcherInterface::class, $config->getDispatcher());
        $this->assertInstanceOf(ServiceCollector::class, $config->getCollector());
        $this->assertEquals(1, $config->getLogLevel());
        $this->assertEquals('@tests/runtime', $config->getProxyCachePath());
        $this->assertEquals(
            [
                LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
            ],
            $config->getDecoratedServices()
        );
        $this->assertEquals(
            [LoggerInterfaceProxy::class, LogCollectorInterface::class],
            $config->getDecoratedServiceConfig(LoggerInterface::class)
        );

        $this->assertTrue($config->hasCollector());
        $this->assertTrue($config->hasDispatcher());
        $this->assertTrue($config->hasDecoratedService(LoggerInterface::class));
        $this->assertTrue($config->hasDecoratedServiceArrayConfig(LoggerInterface::class));
        $this->assertFalse($config->hasDecoratedServiceArrayConfigWithStringKeys(LoggerInterface::class));
        $this->assertFalse($config->hasDecoratedServiceCallableConfig(LoggerInterface::class));
    }
}
