<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\Stream\FilesystemStreamCollector;
use Yiisoft\Yii\Debug\Tests\Shared\AbstractCollectorTestCase;

final class FilesystemStreamCollectorTest extends AbstractCollectorTestCase
{

    /**
     * @param FilesystemStreamCollector $collector
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

    /**
     * @dataProvider dataSkipCollectOnMatchIgnoreReferences
     */
    public function testSkipCollectOnMatchIgnoreReferences(
        string $path,
        callable $before,
        array $ignoredPathPatterns,
        array $ignoredClasses,
        callable $operation,
        callable $after,
        array $result,
    ): void {
        $before($path);

        try {
            $collector = new FilesystemStreamCollector(
                ignoredPathPatterns: $ignoredPathPatterns,
                ignoredClasses: $ignoredClasses,
            );
            $collector->startup();

            $operation($path);

            $collected = $collector->getCollected();
            $collector->shutdown();
        } finally {
            $after($path);
        }
        $this->assertEquals($result, $collected);
    }

    public function dataSkipCollectOnMatchIgnoreReferences(): iterable
    {
        $mkdirBefore = function (string $path) {
            if (is_dir($path)) {
                @rmdir($path);
            }
        };
        $mkdirOperation = function (string $path) {
            mkdir($path, 0777, true);
        };
        $mkdirAfter = $mkdirBefore;

        yield 'mkdir matched' => [
            $path = __DIR__ . '/stub/internal/',
            $mkdirBefore,
            [],
            [],
            $mkdirOperation,
            $mkdirAfter,
            [
                'mkdir' => [
                    ['path' => $path, 'args' => ['mode' => 0777, 'options' => 9]], // 9 for some reasons
                ],
            ],
        ];
        yield 'mkdir ignored by path' => [
            __DIR__ . '/stub/internal/',
            $mkdirBefore,
            ['/' . basename(__FILE__, '.php') . '/'],
            [],
            $mkdirOperation,
            $mkdirAfter,
            [],
        ];
        yield 'mkdir ignored by class' => [
            __DIR__ . '/stub/internal/',
            $mkdirBefore,
            [],
            [self::class],
            $mkdirOperation,
            $mkdirAfter,
            [],
        ];
    }

    protected function getCollector(): CollectorInterface
    {
        return new FilesystemStreamCollector();
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

    protected function checkSummaryData(array $data): void
    {
        parent::checkSummaryData($data);
        $this->assertArrayHasKey('fs_stream', $data);
        $this->assertEquals(
            ['read' => 2, 'mkdir' => 1],
            $data['fs_stream'],
            print_r($data, true),
        );
    }
}
