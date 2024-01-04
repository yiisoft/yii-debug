<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use stdClass;
use Yiisoft\Di\CompositeContainer;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Files\FileHelper;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\ContainerProxyConfig;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\LoggerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Collector\TimelineCollector;

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
        $containerProxy = new ContainerInterfaceProxy(new Container(ContainerConfig::create()), new ContainerProxyConfig());

        $this->assertNotSame(
            $containerProxy,
            $containerProxy->withDecoratedServices(
                [
                    LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollector::class],
                ]
            )
        );
    }

    public function testGetAndHas(): void
    {
        $containerProxy = new ContainerInterfaceProxy($this->createContainer(), $this->createConfig());

        $this->assertTrue($containerProxy->isActive());
        $this->assertTrue($containerProxy->has(LoggerInterface::class));
        $this->assertInstanceOf(LoggerInterfaceProxy::class, $containerProxy->get(LoggerInterface::class));
    }

    public function testGetAndHasWithCallableServices(): void
    {
        $dispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $config = new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => fn (ContainerInterface $container) => $container->get(LoggerInterfaceProxy::class),
                EventDispatcherInterface::class => [
                    EventDispatcherInterfaceProxy::class,
                    EventCollector::class,
                ],
            ],
            $dispatcherMock,
            $this->createServiceCollector(),
            $this->path,
            ContainerInterfaceProxy::LOG_ARGUMENTS
        );
        $containerProxy = new ContainerInterfaceProxy($this->createContainer(), $config);

        $this->assertTrue($containerProxy->isActive());
        $this->assertTrue($containerProxy->has(LoggerInterface::class));

        $containerProxy->get(LogCollector::class)->startup();
        $containerProxy->get(LoggerInterface::class)->log('test', 'test message');
        $this->assertNotEmpty($containerProxy->get(LogCollector::class)->getCollected());
    }

    public function testGetWithArrayConfigWithStringKeys(): void
    {
        $dispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $serviceCollector = $this->createServiceCollector();
        $serviceCollector->startup(); // activate collector

        $config = new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => ['logger' => LoggerInterfaceProxy::class],
                EventDispatcherInterface::class => [
                    EventDispatcherInterfaceProxy::class,
                    EventCollector::class,
                ],
            ],
            $dispatcherMock,
            $serviceCollector,
            $this->path,
            ContainerInterfaceProxy::LOG_ARGUMENTS
        );
        $container = $this->createContainer();
        $containerProxy = new ContainerInterfaceProxy($container, $config);

        $this->assertTrue($containerProxy->isActive());
        $this->assertInstanceOf(LoggerInterface::class, $containerProxy->get(LoggerInterface::class));

        $containerProxy->get(LoggerInterface::class)->log('test', 'test message');
        $this->assertInstanceOf(LoggerInterface::class, $containerProxy->get(LoggerInterface::class));
        $this->assertNotEmpty($config->getCollector()->getCollected());
    }

    public function testGetWithoutConfig(): void
    {
        $dispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $config = new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollector::class],
                EventDispatcherInterface::class,
            ],
            $dispatcherMock,
            $this->createServiceCollector(),
            $this->path,
            ContainerInterfaceProxy::LOG_ARGUMENTS
        );
        $containerProxy = new ContainerInterfaceProxy($this->createContainer(), $config);

        $this->assertInstanceOf(EventDispatcherInterface::class, $containerProxy->get(EventDispatcherInterface::class));
        $this->assertInstanceOf(
            stdClass::class,
            $containerProxy->get(EventDispatcherInterface::class)->dispatch(new stdClass())
        );
    }

    public function testGetAndHasWithWrongId(): void
    {
        $containerProxy = new ContainerInterfaceProxy($this->createContainer(), $this->createConfig());

        $this->assertFalse($containerProxy->has(CollectorInterface::class));

        $this->expectException(ContainerExceptionInterface::class);
        $this->expectExceptionMessage(
            sprintf(
                'No definition or class found or resolvable for "%s" while building "%s".',
                CollectorInterface::class,
                CollectorInterface::class
            )
        );
        $containerProxy->get(CollectorInterface::class);
    }

    public function testGetContainerItself(): void
    {
        $containerProxy = new ContainerInterfaceProxy($this->createContainer(), $this->createConfig());

        $this->assertTrue($containerProxy->has(ContainerInterface::class));

        $container = $containerProxy->get(ContainerInterface::class);
        $this->assertNotNull($container);
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testGetAndHasWithNotService(): void
    {
        $containerProxy = new ContainerInterfaceProxy($this->createContainer(), $this->createConfig());

        $this->assertTrue($containerProxy->has(ListenerProviderInterface::class));
        $this->assertNotNull($containerProxy->get(ListenerProviderInterface::class));
        $this->assertInstanceOf(
            ListenerProviderInterface::class,
            $containerProxy->get(ListenerProviderInterface::class)
        );
    }

    public function testHasThrowsExceptionButErrorInCollectorIsAbsent(): void
    {
        $container = new CompositeContainer();
        $container->attach(new class implements ContainerInterface {
            public function get($id)
            {
                throw new class extends \Exception implements ContainerExceptionInterface {
                };
            }

            public function has($id): bool
            {
                throw new class extends \Exception implements ContainerExceptionInterface {
                };
            }
        });
        $container->attach($container);

        $config = $this->createConfig(ContainerInterfaceProxy::LOG_NOTHING);
        $serviceCollector = $config->getCollector();
        $serviceCollector->startup();
        $containerProxy = new ContainerInterfaceProxy($container, $config);

        $thrown = null;
        try {
            $containerProxy->has('123');
        }catch (\Throwable $e) {
            $thrown = $e;
        }

        $this->assertNotNull($thrown);
        $this->assertNotNull($containerProxy->getCurrentError());

        $data = $serviceCollector->getCollected();
        $this->assertCount(1, $data);
        $this->assertSame(ContainerInterface::class, $data[0]['service']);
        $this->assertSame(CompositeContainer::class, $data[0]['class']);
        $this->assertSame('has', $data[0]['method']);
        $this->assertSame('failed', $data[0]['status']);
        $this->assertNull($data[0]['error']);
    }

    public function testHasThrowsExceptionAndErrorInCollectorIsNotEmpty(): void
    {
        $container = new CompositeContainer();
        $container->attach(new class implements ContainerInterface {
            public function get($id)
            {
                throw new class extends \Exception implements ContainerExceptionInterface {
                };
            }

            public function has($id): bool
            {
                throw new class extends \Exception implements ContainerExceptionInterface {
                };
            }
        });
        $container->attach($container);

        $config = $this->createConfig(ContainerInterfaceProxy::LOG_ERROR);
        $serviceCollector = $config->getCollector();
        $serviceCollector->startup();
        $containerProxy = new ContainerInterfaceProxy($container, $config);

        $thrown = null;
        try {
            $containerProxy->has('123');
        }catch (\Throwable $e) {
            $thrown = $e;
        }

        $this->assertNotNull($thrown);
        $this->assertNotNull($containerProxy->getCurrentError());

        $data = $serviceCollector->getCollected();
        $this->assertCount(1, $data);
        $this->assertSame(ContainerInterface::class, $data[0]['service']);
        $this->assertSame(CompositeContainer::class, $data[0]['class']);
        $this->assertSame('has', $data[0]['method']);
        $this->assertSame('failed', $data[0]['status']);
        $this->assertNotNull($data[0]['error']);
    }

    private function createConfig(int $logLevel = ContainerInterfaceProxy::LOG_ARGUMENTS): ContainerProxyConfig
    {
        return new ContainerProxyConfig(
            true,
            [
                LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollector::class],
                EventDispatcherInterface::class => [
                    EventDispatcherInterfaceProxy::class,
                    EventCollector::class,
                ],
            ],
            $this->createMock(EventDispatcherInterface::class),
            $this->createServiceCollector(),
            $this->path,
            $logLevel
        );
    }

    private function createContainer(): Container
    {
        $config = ContainerConfig::create()
            ->withDefinitions([
                EventDispatcherInterface::class => Dispatcher::class,
                ListenerProviderInterface::class => Provider::class,
                LoggerInterface::class => NullLogger::class,
                LogCollector::class => LogCollector::class,
                EventCollector::class => EventCollector::class,
            ]);
        return new Container($config);
    }

    protected function createServiceCollector(): ServiceCollector
    {
        return new ServiceCollector(new TimelineCollector());
    }
}
