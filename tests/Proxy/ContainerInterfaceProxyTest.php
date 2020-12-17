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
use Yiisoft\Files\FileHelper;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Collector\ServiceCollectorInterface;
use Yiisoft\Yii\Debug\Proxy\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

class ContainerInterfaceProxyTest extends TestCase
{
    private string $path = 'tests/container-proxy';

    protected function tearDown(): void
    {
        parent::tearDown();
        FileHelper::removeDirectory($this->path);
    }

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

    public function testGetAndHasWithCallableServices(): void
    {
        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $config = new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => fn (Container $container) => $container->get(LoggerInterfaceProxy::class),
                EventDispatcherInterface::class => [
                    EventDispatcherInterfaceProxy::class,
                    EventCollectorInterface::class,
                ],
            ],
            $dispatcherMock,
            new ServiceCollector(),
            $this->path,
            1
        );
        $containerProxy = new ContainerInterfaceProxy($this->getContainer(), $config);

        $this->assertTrue($containerProxy->isActive());
        $this->assertTrue($containerProxy->has(LoggerInterface::class));

        $containerProxy->get(LogCollectorInterface::class)->startup();
        $containerProxy->get(LoggerInterface::class)->log('test', 'test message');
        $this->assertNotEmpty($containerProxy->get(LogCollectorInterface::class)->getCollected());
    }

    public function testGetWithArrayConfigWithStringKeys(): void
    {
        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $serviceCollector = new ServiceCollector();
        $serviceCollector->startup(); // activate collector

        $config = new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => ['logger' => LoggerInterfaceProxy::class],
                EventDispatcherInterface::class => [
                    EventDispatcherInterfaceProxy::class,
                    EventCollectorInterface::class,
                ],
            ],
            $dispatcherMock,
            $serviceCollector,
            $this->path,
            1
        );
        $container = $this->getContainer();
        $containerProxy = new ContainerInterfaceProxy($container, $config);

        $this->assertTrue($containerProxy->isActive());
        $this->assertInstanceOf(LoggerInterface::class, $containerProxy->get(LoggerInterface::class));

        $containerProxy->get(LoggerInterface::class)->log('test','test message');
        $this->assertInstanceOf(LoggerInterface::class, $containerProxy->get(LoggerInterface::class));
        $this->assertNotEmpty($config->getCollector()->getCollected());
    }

    public function testGetWithoutConfig(): void
    {
        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $config = new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
                EventDispatcherInterface::class,
            ],
            $dispatcherMock,
            new ServiceCollector(),
            $this->path,
            1
        );
        $containerProxy = new ContainerInterfaceProxy($this->getContainer(), $config);

        $this->assertInstanceOf(EventDispatcherInterface::class, $containerProxy->get(EventDispatcherInterface::class));
        $this->assertInstanceOf(
            \stdClass::class,
            $containerProxy->get(EventDispatcherInterface::class)->dispatch(new \stdClass())
        );
    }

    public function testGetAndHasWithWrongId(): void
    {
        $this->expectException(ContainerExceptionInterface::class);
        $this->expectExceptionMessage(sprintf('No definition for %s', ServiceCollectorInterface::class));

        $containerProxy = new ContainerInterfaceProxy($this->getContainer(), $this->getConfig());

        $containerProxy->has(ServiceCollectorInterface::class);
        $containerProxy->get(ServiceCollectorInterface::class);
    }

    public function testGetAndHasWithNotService(): void
    {
        $containerProxy = new ContainerInterfaceProxy($this->getContainer(), $this->getConfig());

        $this->assertTrue($containerProxy->has(ListenerProviderInterface::class));
        $this->assertNotNull($containerProxy->get(ListenerProviderInterface::class));
        $this->assertInstanceOf(
            ListenerProviderInterface::class,
            $containerProxy->get(ListenerProviderInterface::class)
        );
    }

    private function getConfig(): ContainerProxyConfig
    {
        $dispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        return new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
                EventDispatcherInterface::class => [
                    EventDispatcherInterfaceProxy::class,
                    EventCollectorInterface::class,
                ],
            ],
            $dispatcherMock,
            new ServiceCollector(),
            $this->path,
            1
        );
    }

    private function getContainer(): Container
    {
        return new Container(
            [
                EventDispatcherInterface::class => Dispatcher::class,
                ListenerProviderInterface::class => Provider::class,
                LoggerInterface::class => NullLogger::class,
                LogCollectorInterface::class => LogCollector::class,
                EventCollectorInterface::class => EventCollector::class,
            ]
        );
    }
}
