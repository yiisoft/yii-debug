<?php

declare(strict_types=1);

use Yiisoft\ErrorHandler\Event\ApplicationError;
use Yiisoft\Middleware\Dispatcher\Event\AfterMiddleware;
use Yiisoft\Middleware\Dispatcher\Event\BeforeMiddleware;
use Yiisoft\Profiler\ProfilerInterface;
use Yiisoft\View\Event\WebView\AfterRender;
use Yiisoft\Yii\Debug\Collector\Web\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\Web\RequestCollector;
use Yiisoft\Yii\Debug\Collector\Web\WebAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\Web\WebViewCollector;
use Yiisoft\Yii\Debug\Collector\ExceptionCollector;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Http\Event\AfterEmit;
use Yiisoft\Yii\Http\Event\AfterRequest;
use Yiisoft\Yii\Http\Event\ApplicationShutdown;
use Yiisoft\Yii\Http\Event\ApplicationStartup;
use Yiisoft\Yii\Http\Event\BeforeRequest;

if (!(bool)($params['yiisoft/yii-debug']['enabled'] ?? false)) {
    return [];
}

return [
    ApplicationStartup::class => [
        [WebAppInfoCollector::class, 'collect'],
    ],
    ApplicationShutdown::class => [
        [WebAppInfoCollector::class, 'collect'],
    ],
    BeforeRequest::class => [
        [Debugger::class, 'startup'],
        [WebAppInfoCollector::class, 'collect'],
        [RequestCollector::class, 'collect'],
    ],
    AfterRequest::class => [
        [WebAppInfoCollector::class, 'collect'],
        [RequestCollector::class, 'collect'],
    ],
    AfterEmit::class => [
        [ProfilerInterface::class, 'flush'],
        [WebAppInfoCollector::class, 'collect'],
        [Debugger::class, 'shutdown'],
    ],
    BeforeMiddleware::class => [
        [MiddlewareCollector::class, 'collect'],
    ],
    AfterMiddleware::class => [
        [MiddlewareCollector::class, 'collect'],
    ],
    AfterRender::class => [
        [WebViewCollector::class, 'collect'],
    ],
    ApplicationError::class => [
        [ExceptionCollector::class, 'collect'],
    ],
];
