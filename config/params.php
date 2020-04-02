<?php

use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollectorInterface;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\RouterCollector;

/**
 * @var $params array
 */

return [
    'debugger.collectors' => [
        LogCollectorInterface::class,
        EventCollectorInterface::class,
        RequestCollector::class,
        RouterCollector::class,
        MiddlewareCollector::class,
        ServiceCollectorInterface::class
    ],
    'debugger.trackedServices' => [
        LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
        EventDispatcherInterface::class => [EventDispatcherInterfaceProxy::class, EventCollectorInterface::class],
    ],
    'debugger.logLevel' => ContainerProxy::LOG_ARGUMENTS | ContainerProxy::LOG_RESULT | ContainerProxy::LOG_ERROR,
    'debugger.path' => '@runtime/debug',
];
