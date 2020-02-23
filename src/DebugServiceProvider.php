<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Dispatcher\DebugShutdownDispatcher;
use Yiisoft\Yii\Debug\Dispatcher\DebugStartupDispatcher;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerProxy;

class DebugServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);

        $container->setMultiple(
            [
                // interfaces overriding
                LoggerInterface::class => function (ContainerInterface $container) use ($logger) {
                    return new LoggerProxy($logger, $container->get(LogCollector::class));
                },
                EventDispatcherInterface::class => function (ContainerInterface $container) use ($dispatcher) {
                    $compositeDispatcher = new CompositeDispatcher();
                    $compositeDispatcher->attach($container->get(DebugStartupDispatcher::class));
                    $compositeDispatcher->attach($container->get(DebugEventDispatcher::class));
                    $compositeDispatcher->attach($dispatcher);
                    $compositeDispatcher->attach($container->get(DebugShutdownDispatcher::class));

                    return new EventDispatcherProxy($compositeDispatcher, $container->get(EventCollector::class));
                },
                // collectors initialization
                LogCollector::class => fn() => new LogCollector(),
                EventCollector::class => fn() => new EventCollector(),
            ]
        );
    }
}
