<?php

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher;
use Yiisoft\Yii\Debug\DebugEventDispatcher;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Dispatcher\DebugShutdownDispatcher;
use Yiisoft\Yii\Debug\Dispatcher\DebugStartupDispatcher;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\RouterCollector;
use Yiisoft\Yii\Web\Event\AfterMiddleware;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;
use Yiisoft\Yii\Web\Event\ApplicationStartup;
use Yiisoft\Yii\Web\Event\BeforeMiddleware;
use Yiisoft\Yii\Web\Event\BeforeRequest;

/**
 * @var $params array
 */

return [
    'debugger.collectors' => [
        LogCollector::class,
        EventCollector::class,
        RequestCollector::class,
        RouterCollector::class,
        MiddlewareCollector::class,
        ServiceCollector::class
    ],
    'debugger.trackedServices' => [
        LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
        EventDispatcherInterface::class => function (ContainerInterface $container) {
            $dispatcher = $container->get(EventDispatcherInterface::class);
            $compositeDispatcher = new CompositeDispatcher();
            $compositeDispatcher->attach($container->get(DebugStartupDispatcher::class));
            $compositeDispatcher->attach($container->get(DebugEventDispatcher::class));
            $compositeDispatcher->attach($dispatcher);
            $compositeDispatcher->attach($container->get(DebugShutdownDispatcher::class));

            return new EventDispatcherInterfaceProxy($compositeDispatcher, $container->get(EventCollectorInterface::class));
        },
    ],
    'debugger.logLevel' => ContainerProxy::LOG_ARGUMENTS | ContainerProxy::LOG_RESULT | ContainerProxy::LOG_ERROR,
    'debugger.eventHandlers' => [
        ApplicationStartup::class => [
            static function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        ApplicationShutdown::class => [
            static function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        BeforeRequest::class => [
            static function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        AfterRequest::class => [
            static function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        BeforeMiddleware::class => [
            static function (ContainerInterface $container) {
                return [$container->get(MiddlewareCollector::class), 'collect'];
            },
        ],
        AfterMiddleware::class => [
            static function (ContainerInterface $container) {
                return [$container->get(MiddlewareCollector::class), 'collect'];
            },
        ],
    ],
];
