<?php

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\Log\Logger;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

return [
    TargetInterface::class => MemTarget::class,
    LoggerInterface::class => function (ContainerInterface $container) {
        return new LogCollector($container->get(Logger::class));
    },
    EventDispatcherInterface::class => function (ContainerInterface $container) {
        return new EventCollector($container->get(Dispatcher::class));
    },
];
