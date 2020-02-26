<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;

final class EventCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\EventCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new \stdClass());
    }

    protected function getCollector(): CollectorInterface
    {
        return new EventCollector();
    }
}
