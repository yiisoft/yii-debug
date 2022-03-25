<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Tests\Support\DummyEvent;

final class EventCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\EventCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new DummyEvent(), __FILE__ . ':' . __LINE__);
    }

    protected function getCollector(): CollectorInterface
    {
        return new EventCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);
        $this->assertFileExists($collector->getCollected()[0]['file']);
    }
}
