<?php


use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;

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
];
