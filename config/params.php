<?php


use Psr\Container\ContainerInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
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
        MiddlewareCollector::class,
    ],
    'debugger.event_handlers' => [
        ApplicationStartup::class => [
            function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        ApplicationShutdown::class => [
            function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        BeforeRequest::class => [
            function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        AfterRequest::class => [
            function (ContainerInterface $container) {
                return [$container->get(RequestCollector::class), 'collect'];
            },
        ],
        BeforeMiddleware::class => [
            function (ContainerInterface $container) {
                return [$container->get(MiddlewareCollector::class), 'collect'];
            },
        ],
        AfterMiddleware::class => [
            function (ContainerInterface $container) {
                return [$container->get(MiddlewareCollector::class), 'collect'];
            },
        ],
    ],
];
