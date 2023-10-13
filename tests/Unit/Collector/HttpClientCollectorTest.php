<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use GuzzleHttp\Psr7\Response;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\HttpClientCollector;
use Yiisoft\Yii\Debug\Collector\TimelineCollector;
use Yiisoft\Yii\Debug\Tests\Shared\AbstractCollectorTestCase;

final class HttpClientCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|HttpClientCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(
            new \GuzzleHttp\Psr7\Request('GET', 'http://example.com'),
            startTime: 10.10,
            line: 'file1:123',
            uniqueId: 'test1',
        );
        $collector->collect(
            new \GuzzleHttp\Psr7\Request('GET', 'http://yiiframework.com'),
            startTime: 12.10,
            line: 'file2:555',
            uniqueId: 'test2'
        );

        $collector->collectTotalTime(
            new Response(200, [], 'test'),
            endTime: 13.10,
            uniqueId: 'test1'
        );
        $collector->collectTotalTime(
            new Response(200, [], 'test'),
            endTime: 12.20,
            uniqueId: 'test2'
        );
    }

    protected function getCollector(): CollectorInterface
    {
        return new HttpClientCollector(new TimelineCollector());
    }

    protected function checkCollectedData(array $data): void
    {
        parent::checkCollectedData($data);

        $this->assertCount(2, $data);
    }

    protected function checkSummaryData(array $data): void
    {
        parent::checkSummaryData($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('http', $data);
        $this->assertCount(2, $data['http']);
        $this->assertArrayHasKey('count', $data['http']);
        $this->assertArrayHasKey('totalTime', $data['http']);

        $this->assertEquals(2, $data['http']['count']);
        $this->assertEquals(3.1, round($data['http']['totalTime'], 1));
    }
}
