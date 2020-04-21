<?php

use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Web\Event\AfterEmit;
use Yiisoft\Yii\Web\Event\AfterMiddleware;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;
use Yiisoft\Yii\Web\Event\ApplicationStartup;
use Yiisoft\Yii\Web\Event\BeforeMiddleware;
use Yiisoft\Yii\Web\Event\BeforeRequest;

if (!(bool)($params['debugger.enabled'] ?? false)) {
    return [];
}

return [
    ApplicationStartup::class => [
        [RequestCollector::class, 'collect'],
    ],
    ApplicationShutdown::class => [
        [RequestCollector::class, 'collect'],
    ],
    BeforeRequest::class => [
        [Debugger::class, 'startup'],
        [RequestCollector::class, 'collect'],
    ],
    AfterRequest::class => [
        [RequestCollector::class, 'collect'],
    ],
    AfterEmit::class => [
        [RequestCollector::class, 'collect'],
        [Debugger::class, 'shutdown'],
    ],
    BeforeMiddleware::class => [
        [MiddlewareCollector::class, 'collect'],
    ],
    AfterMiddleware::class => [
        [MiddlewareCollector::class, 'collect'],
    ],
];
