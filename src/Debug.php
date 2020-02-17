<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\CompositeContainer;
use Yiisoft\Di\Container;
use Yiisoft\EventDispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class Debug
{
    public static function init(ContainerInterface $container): ContainerInterface
    {
        $definitions = self::getDefinitions($container);
        $debugContainer = new Container($definitions);

        $newContainer = new CompositeContainer();
        $newContainer->attach($debugContainer);
        $newContainer->attach($newContainer);

        return $newContainer;
    }

    private static function getDefinitions(ContainerInterface $originContainer): array
    {
        return [
            TargetInterface::class => MemTarget::class,
            LoggerInterface::class => function () use ($originContainer) {
                return new LogCollector($originContainer->get(LoggerInterface::class));
            },
            EventDispatcherInterface::class => function () use ($originContainer) {
                return new EventCollector($originContainer->get(Dispatcher::class));
            },
            ListenerProviderInterface::class => function () use ($originContainer) {
                return new RequestCollector($originContainer->get(Provider::class));
            },
        ];
    }
}
