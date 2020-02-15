<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

abstract class AbstractCollectorTestCase extends TestCase
{
    /**
     * @dataProvider targetProvider()
     * @param \Yiisoft\Yii\Debug\Target\TargetInterface $target
     */
    public function testExport(TargetInterface $target): void
    {
        $collector = $this->getCollector($target);
        $this->assertEmpty($target->getData());

        $collector->export();
        $this->assertNotEmpty($target->getData());
    }

    public function targetProvider(): array
    {
        return [
            [new MemTarget()],
        ];
    }

    abstract protected function getCollector(TargetInterface $target): CollectorInterface;
}
