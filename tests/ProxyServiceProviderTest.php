<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Yiisoft\Di\Container;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\ProxyServiceProvider;

class ProxyServiceProviderTest extends TestCase
{
    /**
     * @throws \Yiisoft\Factory\Exceptions\InvalidConfigException
     *
     * @covers \Yiisoft\Yii\Debug\ProxyServiceProvider::register()
     */
    public function testRegister(): void
    {
        $provider = new ProxyServiceProvider();
        $container = new Container(
            [
                LoggerInterface::class => NullLogger::class,
                EventDispatcherInterface::class => Dispatcher::class,
                ListenerProviderInterface::class => Provider::class,
            ]
        );
        $provider->register($container);

        $this->assertInstanceOf(ContainerProxy::class, $container->get(ContainerInterface::class));
    }
}
