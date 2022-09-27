<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\ServiceProviderInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\LoggerInterfaceProxy;

final class DebugServiceProvider implements ServiceProviderInterface
{
    public function getDefinitions(): array
    {
        return [];
    }

    public function getExtensions(): array
    {
        return [
            LoggerInterface::class => static fn(ContainerInterface $container, LoggerInterface $logger) => new LoggerInterfaceProxy($logger, $container->get(LogCollector::class)),
            EventDispatcherInterface::class => static fn(ContainerInterface $container, EventDispatcherInterface $dispatcher) => new EventDispatcherInterfaceProxy($dispatcher, $container->get(EventCollector::class)),
        ];
    }
}
