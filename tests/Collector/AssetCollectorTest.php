<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\Yii\Debug\Collector\AssetCollector;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

final class AssetCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\AssetCollector|\Yiisoft\Yii\Debug\Collector\CollectorInterface $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new AssetBundle());
    }

    protected function getCollector(): CollectorInterface
    {
        return new AssetCollector();
    }
}
