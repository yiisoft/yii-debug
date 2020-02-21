<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Di\Container;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\EventDispatcher\Provider\CompositeProvider;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Target\FileTarget;
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
                TargetInterface::class => function (ContainerInterface $container) {
                    $runtime = $container->get(Aliases::class)->get('@runtime');
                    $id = time();

                    return new FileTarget("$runtime/debug/$id.data");
                },
                LoggerInterface::class => function (ContainerInterface $container) use ($logger) {
                    return new LogCollector($logger);
                },
                EventDispatcherInterface::class => function (ContainerInterface $container) use ($dispatcher) {
                    $compositeDispatcher = new \Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher();
                    $compositeDispatcher->attach(new \Yiisoft\EventDispatcher\Dispatcher\Dispatcher($container->get(DebugListenerProvider::class)));
                    $compositeDispatcher->attach($dispatcher);

                    return new EventCollector($compositeDispatcher);
                },
                ListenerProviderInterface::class => function (ContainerInterface $container) use ($listenerProvider) {
                    $provider = new CompositeProvider();
                    $provider->attach($listenerProvider);
                    $provider->attach($container->get(DebugListenerProvider::class));

                    return new RequestCollector($provider);
                },
                Debugger::class => function (ContainerInterface $container) {
                    return new Debugger(
                        $container->get(TargetInterface::class),
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
