<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;

final class ServiceCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\ServiceCollectorInterface $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $time = microtime(true);
        $collector->collect('test', \stdClass::class, 'test', [], '', 'success', null, $time, $time + 1);
    }

    protected function getCollector(): CollectorInterface
    {
        return new ServiceCollector();
    }
}
