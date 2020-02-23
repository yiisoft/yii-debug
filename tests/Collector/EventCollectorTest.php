<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;

class EventCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\EventCollector $collector
     */
    protected function somethingDoTestExport(CollectorInterface $collector): void
    {
        $collector->dispatch(new \stdClass());
    }

    protected function getCollector(): CollectorInterface
    {
        return new EventCollector();
    }
}
