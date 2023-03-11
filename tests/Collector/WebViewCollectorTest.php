<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\View\Event\WebView\AfterRender;
use Yiisoft\View\WebView;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\Web\WebViewCollector;

final class WebViewCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|WebViewCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new AfterRender(new WebView(__DIR__, $this->createMock(EventDispatcherInterface::class)), __FILE__, ['foo' => 'bar'], 'test content'));
    }

    protected function getCollector(): CollectorInterface
    {
        return new WebViewCollector();
    }
}
