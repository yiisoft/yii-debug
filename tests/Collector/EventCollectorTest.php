<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Tests\Support\DummyEvent;

final class EventCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|EventCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new DummyEvent(), __FILE__ . ':' . __LINE__);
    }

    protected function getCollector(): CollectorInterface
    {
        return new EventCollector();
    }

    protected function checkCollectedData(array $data): void
    {
        $this->assertFileExists($data[0]['file']);
    }
}
