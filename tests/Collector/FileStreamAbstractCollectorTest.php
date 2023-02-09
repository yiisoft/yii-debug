<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\FileStreamCollector;

final class FileStreamAbstractCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|FileStreamCollector $collector
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
        $collector->collect(
            operation: 'mkdir',
            path: __DIR__,
            args: ['recursive'],
        );
    }

    public function testCollectWithInactiveCollector(): void
    {
        $collector = $this->getCollector();
        $this->collectTestData($collector);

        $collected = $collector->getCollected();
        $this->assertEmpty($collected);
    }

    protected function getCollector(): CollectorInterface
    {
        return new FileStreamCollector();
    }

    protected function checkCollectedData(array $data): void
    {
        parent::checkCollectedData($data);
        $collected = $data;
        $this->assertCount(2, $collected);

        $this->assertCount(2, $collected['read']);
        $this->assertEquals([
            ['path' => __FILE__, 'args' => ['arg1' => 'v1', 'arg2' => 'v2']],
            ['path' => __FILE__, 'args' => ['arg3' => 'v3', 'arg4' => 'v4']],
        ], $collected['read']);

        $this->assertCount(1, $collected['mkdir']);
        $this->assertEquals([
            ['path' => __DIR__, 'args' => ['recursive']],
        ], $collected['mkdir']);
    }

    protected function checkIndexData(array $data): void
    {
        parent::checkIndexData($data);
        $this->assertArrayHasKey('file', $data);
        $this->assertEquals(
            ['read' => 2, 'mkdir' => 1],
            $data['file'],
            print_r($data, true),
        );
    }
}
