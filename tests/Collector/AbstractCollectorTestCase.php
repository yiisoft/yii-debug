<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

abstract class AbstractCollectorTestCase extends TestCase
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
        $this->assertNotEmpty($collector->collected());
    }
}
