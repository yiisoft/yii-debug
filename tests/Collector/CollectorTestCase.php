<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

abstract class CollectorTestCase extends TestCase
{
    public function testCollect(): void
    {
        $collector = $this->getCollector();
        $collector->startup();
        $this->collectTestData($collector);
        $this->checkCollectedData($collector);
        $collector->shutdown();
    }

    abstract protected function getCollector(): CollectorInterface;

    abstract protected function collectTestData(CollectorInterface $collector): void;

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        $this->assertNotEmpty($collector->getCollected());
    }

    protected function checkIndexData(CollectorInterface $collector): void
    {
        if ($collector instanceof IndexCollectorInterface) {
            $this->assertNotEmpty($collector->getIndexData());
        }
    }
}
