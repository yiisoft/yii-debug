<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

abstract class AbstractCollectorTestCase extends TestCase
{
    public function testExport(): void
    {
        $collector = $this->getCollector();
        $collector->startup();
        $this->somethingDoTestExport($collector);
        $this->assertExportedData($collector);
        $collector->shutdown();
    }

    abstract protected function getCollector(): CollectorInterface;

    abstract protected function somethingDoTestExport(CollectorInterface $collector): void;

    protected function assertExportedData(CollectorInterface $collector): void
    {
        $this->assertNotEmpty($collector->collected());
    }
}
