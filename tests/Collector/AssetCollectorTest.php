<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Assets\AssetBundle;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\Web\AssetCollector;

final class AssetCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param AssetCollector|CollectorInterface $collector
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
