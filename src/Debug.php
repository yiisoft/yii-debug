<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\CompositeContainer;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class Debug
{
    public static function init(ContainerInterface $container): ContainerInterface
    {
        $compositeContainer = new CompositeContainer();

        $definitions = self::getDefinitions($container);
        // extending only for debugging that to have difference between original container and debug container
        $debugContainer = new class($definitions) extends Container{};
        $debugContainer->set(ContainerInterface::class, fn($c) => $compositeContainer);

        $compositeContainer->attach($container);
        $compositeContainer->attach($debugContainer);

        return $compositeContainer;
    }

    private static function getDefinitions(ContainerInterface $originContainer): array
    {
        return [
            TargetInterface::class => MemTarget::class,
            LoggerInterface::class => function () use ($originContainer) {
                return new LogCollector($originContainer->get(LoggerInterface::class));
            },
            EventDispatcherInterface::class => function () use ($originContainer) {
                return new EventCollector($originContainer->get(EventDispatcherInterface::class));
            },
            ListenerProviderInterface::class => function () use ($originContainer) {
                return new RequestCollector($originContainer->get(ListenerProviderInterface::class));
            },
        ];
    }
}
