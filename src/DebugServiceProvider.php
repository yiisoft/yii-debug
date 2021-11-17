<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\ServiceProviderInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

final class DebugServiceProvider implements ServiceProviderInterface
{
    public function getDefinitions(): array
    {
        return [];
    }

    public function getExtensions(): array
    {
        return [
            LoggerInterface::class => static function (ContainerInterface $container, LoggerInterface $logger) {
                return new LoggerInterfaceProxy($logger, $container->get(LogCollector::class));
            },
            EventDispatcherInterface::class => static function (ContainerInterface $container, EventDispatcherInterface $dispatcher) {
                return new EventDispatcherInterfaceProxy($dispatcher, $container->get(EventCollector::class));
            },
        ];
    }
}
