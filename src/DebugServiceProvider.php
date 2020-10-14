<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

final class DebugServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        $logger = $container->get(LoggerInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);

        /**
         * @psalm-suppress InaccessibleMethod
         */
        $container->setMultiple(
            [
                // interfaces overriding
                LoggerInterface::class => static function (ContainerInterface $container) use ($logger) {
                    return new LoggerInterfaceProxy($logger, $container->get(LogCollector::class));
                },
                EventDispatcherInterface::class => static function (ContainerInterface $container) use ($dispatcher) {
                    return new EventDispatcherInterfaceProxy($dispatcher, $container->get(EventCollector::class));
                },
            ]
        );
    }
}
