<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Dispatcher\DebugShutdownDispatcher;
use Yiisoft\Yii\Debug\Dispatcher\DebugStartupDispatcher;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

final class DebugServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);

        $container->setMultiple(
            [
                // interfaces overriding
                LoggerInterface::class => static function (ContainerInterface $container) use ($logger) {
                    return new LoggerInterfaceProxy($logger, $container->get(LogCollector::class));
                },
                EventDispatcherInterface::class => static function (ContainerInterface $container) use ($dispatcher) {
                    $compositeDispatcher = new CompositeDispatcher();
                    $compositeDispatcher->attach($container->get(DebugStartupDispatcher::class));
                    $compositeDispatcher->attach($container->get(DebugEventDispatcher::class));
                    $compositeDispatcher->attach($dispatcher);
                    $compositeDispatcher->attach($container->get(DebugShutdownDispatcher::class));

                    return new EventDispatcherInterfaceProxy($compositeDispatcher, $container->get(EventCollector::class));
                },
            ]
        );
    }
}
