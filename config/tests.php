<?php

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\Provider;
use Yiisoft\Log\Logger;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

return [
    TargetInterface::class => MemTarget::class,
    LoggerInterface::class => function (ContainerInterface $container) {
        return new LogCollector(
            $container->get(Logger::class)
        );
    },
    EventDispatcherInterface::class => function (ContainerInterface $container) {
        return new EventCollector(
            $container->get(Dispatcher::class)
        );
    },
    ListenerProviderInterface::class => function (ContainerInterface $container) {
        return new RequestCollector(
            $container->get(Dispatcher::class)
        );
    },
    Dispatcher::class => function () {
        return new Yiisoft\EventDispatcher\Dispatcher\Dispatcher(new Provider());
    },
];
