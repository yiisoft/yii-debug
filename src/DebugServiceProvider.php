<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Aggregate;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class DebugServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);
        $listenerProvider = $container->get(ListenerProviderInterface::class);

        $container->setMultiple(
            [
                TargetInterface::class => MemTarget::class,
                LoggerInterface::class => function (ContainerInterface $container) use ($logger) {
                    $collector = new LogCollector($logger);
                    $collector->setTarget($container->get(TargetInterface::class));

                    return $collector;
                },
                EventDispatcherInterface::class => function (ContainerInterface $container) use ($dispatcher) {
                    $compositeDispatcher = new CompositeDispatcher();
                    $compositeDispatcher->attach(new Dispatcher($container->get(DebugListenerProvider::class)));
                    $compositeDispatcher->attach($dispatcher);

                    $collector = new EventCollector($compositeDispatcher);
                    $collector->setTarget($container->get(TargetInterface::class));

                    return $collector;
                },
                ListenerProviderInterface::class => function (ContainerInterface $container) use ($listenerProvider) {
                    $provider = new Aggregate();
                    $provider->attach($listenerProvider);
                    $provider->attach($container->get(DebugListenerProvider::class));

                    $collector = new RequestCollector($provider);
                    $collector->setTarget($container->get(TargetInterface::class));

                    return $collector;
                },
                Debugger::class => function (ContainerInterface $container) {
                    return new Debugger(
                        ...[
                               $container->get(LoggerInterface::class),
                               $container->get(EventDispatcherInterface::class),
                               $container->get(ListenerProviderInterface::class),
                           ]
                    );
                },
            ]
        );
    }
}
