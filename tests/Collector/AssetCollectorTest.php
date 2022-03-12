<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Assets\AssetBundle;
use Yiisoft\View\Event\WebView\AfterRender;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Debug\Collector\AssetCollector;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\WebViewCollector;

final class AssetCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\AssetCollector $collector
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
