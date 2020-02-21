<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use hiqdev\composer\config\Builder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

abstract class AbstractCollectorTestCase extends TestCase
{
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $config = require Builder::path('tests');

        $this->container = new Container($config);
    }

    /**
     * @dataProvider targetProvider()
     * @param \Yiisoft\Yii\Debug\Target\TargetInterface $target
     */
    public function testExport(TargetInterface $target): void
    {
        $collector = $this->getCollector();
        $collector->startup();
        $this->assertEmpty($target->getData());
        $this->somethingDoTestExport($collector);
        $this->assertExportedData($collector);
        $collector->shutdown();
    }

    public function targetProvider(): array
    {
        return [
            [new MemTarget()],
        ];
    }

    abstract protected function getCollector(): CollectorInterface;

    abstract protected function somethingDoTestExport(CollectorInterface $collector): void;

    protected function assertExportedData(CollectorInterface $collector): void
    {
        $this->assertNotEmpty($collector->collect());
    }
}
