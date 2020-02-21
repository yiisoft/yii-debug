<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Di\Container;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Dispatcher\DebugShutdownDispatcher;
use Yiisoft\Yii\Debug\Dispatcher\DebugStartupDispatcher;
use Yiisoft\Yii\Debug\Target\FileTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class DebugServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);

        $container->setMultiple(
            [
                TargetInterface::class => function (ContainerInterface $container) {
                    $runtime = $container->get(Aliases::class)->get('@runtime');
                    $id = time();

                    return new FileTarget("$runtime/debug/$id.data");
                },
                LogCollector::class => fn() => new LogCollector($logger),
                LoggerInterface::class => LogCollector::class,
                MiddlewareCollector::class => fn() => new MiddlewareCollector(),
                RequestCollector::class => function () {
                    return new RequestCollector();
                },
                EventCollector::class => function (ContainerInterface $container) use ($dispatcher) {
                    $compositeDispatcher = new CompositeDispatcher();
                    $compositeDispatcher->attach($container->get(DebugStartupDispatcher::class));
                    $compositeDispatcher->attach($container->get(RequestCollector::class));
                    $compositeDispatcher->attach($container->get(MiddlewareCollector::class));
                    $compositeDispatcher->attach($dispatcher);
                    $compositeDispatcher->attach($container->get(DebugShutdownDispatcher::class));

                    return new EventCollector($compositeDispatcher);
                },
                EventDispatcherInterface::class => EventCollector::class,
                Debugger::class => function (ContainerInterface $container) {
                    return new Debugger(
                        $container->get(TargetInterface::class),
                        $container->get(LogCollector::class),
                        $container->get(EventCollector::class),
                        $container->get(RequestCollector::class),
                        $container->get(MiddlewareCollector::class),
                    );
                },
            ]
        );
    }
}
