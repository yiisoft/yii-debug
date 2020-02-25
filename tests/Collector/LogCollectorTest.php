<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Log\LogLevel;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollector;

class LogCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\LogCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(LogLevel::ALERT, 'test', ['context']);
    }

    protected function getCollector(): CollectorInterface
    {
        return new LogCollector();
    }
}
