<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

abstract class AbstractCollectorTestCase extends TestCase
{
    public function testCollect(): void
    {
        $indexData = null;
        $collector = $this->getCollector();

        $collector->startup();
        $this->collectTestData($collector);
        $data = $collector->getCollected();
        if ($collector instanceof IndexCollectorInterface) {
            $indexData = $collector->getIndexData();
        }
        $collector->shutdown();

        $this->assertSame($collector::class, $collector->getName());
        $this->checkCollectedData($data);
        if ($collector instanceof IndexCollectorInterface) {
            $this->checkIndexData($indexData);
        }
    }

    abstract protected function getCollector(): CollectorInterface;

    abstract protected function collectTestData(CollectorInterface $collector): void;

    protected function checkCollectedData(array $data): void
    {
        $this->assertNotEmpty($data);
    }

    protected function checkIndexData(array $data): void
    {
        $this->assertNotEmpty($data);
    }
}
