<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\Stream\HttpStreamCollector;

final class HttpStreamCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|HttpStreamCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(
            operation: 'read',
            path: __FILE__,
            args: ['arg1' => 'v1', 'arg2' => 'v2'],
        );
        $collector->collect(
            operation: 'read',
            path: __FILE__,
            args: ['arg3' => 'v3', 'arg4' => 'v4'],
        );
    }

    protected function getCollector(): CollectorInterface
    {
        return new HttpStreamCollector();
    }

    protected function checkCollectedData(array $data): void
    {
        parent::checkCollectedData($data);
        $collected = $data;
        $this->assertCount(1, $collected);

        $this->assertCount(2, $collected['read']);
        $this->assertEquals([
            ['uri' => __FILE__, 'args' => ['arg1' => 'v1', 'arg2' => 'v2']],
            ['uri' => __FILE__, 'args' => ['arg3' => 'v3', 'arg4' => 'v4']],
        ], $collected['read']);
    }

    protected function checkSummaryData(array $data): void
    {
        parent::checkSummaryData($data);
        $this->assertArrayHasKey('http_stream', $data);
        $this->assertEquals(['read' => 2], $data['http_stream']);
    }
}
