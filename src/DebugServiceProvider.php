<?php

namespace Yiisoft\Yii\Debug;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
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
                LoggerInterface::class => function () use ($logger) {
                    return new LogCollector($logger);
                },
                EventDispatcherInterface::class => function () use ($dispatcher) {
                    return new EventCollector($dispatcher);
                },
                ListenerProviderInterface::class => function () use ($listenerProvider) {
                    return new RequestCollector($listenerProvider);
                },
            ]
        );
    }
}
