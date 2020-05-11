<?php

use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\ConsoleAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollectorInterface;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\WebAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\RouterCollector;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

/**
 * @var $params array
 */

return [
    'debugger.collectors' => [
        LogCollectorInterface::class,
        EventCollectorInterface::class,
        ServiceCollectorInterface::class
    ],
    'debugger.collectors.web' => [
        WebAppInfoCollector::class,
        RouterCollector::class,
        MiddlewareCollector::class,
    ],
    'debugger.collectors.console' => [
        ConsoleAppInfoCollector::class,
        \Yiisoft\Yii\Debug\Collector\CommandCollector::class,
    ],
    'debugger.trackedServices' => [
        LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
        EventDispatcherInterface::class => [EventDispatcherInterfaceProxy::class, EventCollectorInterface::class],
    ],
    'debugger.logLevel' => ContainerProxy::LOG_ARGUMENTS | ContainerProxy::LOG_RESULT | ContainerProxy::LOG_ERROR,
    'debugger.path' => '@runtime/debug',
];
