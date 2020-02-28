<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
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
                    return new LoggerProxy($logger, $container->get(LogCollectorInterface::class));
                },
                EventDispatcherInterface::class => function (ContainerInterface $container) use ($dispatcher) {
                    $debugger =  $container->get(Debugger::class);
                    $debugEventDispatcher =  $dispatcher = $container->get(DebugEventDispatcher::class);
                    $collector = $container->get(EventCollectorInterface::class);
                    return new EventDispatcherProxy($dispatcher, $debugEventDispatcher, $collector, $debugger);
                },
            ]
        );
    }
}
