<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use Yiisoft\Files\FileHelper;
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
            $path,
            $mkdirBefore,
            ['/' . basename(__FILE__, '.php') . '/'],
            [],
            $mkdirOperation,
            $mkdirAfter,
            [],
        ];
        yield 'mkdir ignored by class' => [
            $path,
            $mkdirBefore,
            [],
            [self::class],
            $mkdirOperation,
            $mkdirAfter,
            [],
        ];

        $renameBefore = function (string $path) {
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            if (!is_file($path)) {
                touch($path);
            }
        };
        $renameOperation = function (string $path) {
            rename($path, $path. '.renamed');
        };
        $renameAfter = function (string $path) {
            FileHelper::removeDirectory(dirname($path));
        };

        yield 'rename matched' => [
            $path = __DIR__ . '/stub/file-to-rename.txt',
            $renameBefore,
            [],
            [],
            $renameOperation,
            $renameAfter,
            [
                'rename' => [
                    ['path' => $path, 'args' => ['path_to' => $path . '.renamed']],
                ],
            ],
        ];
        yield 'rename ignored by path' => [
            $path,
            $renameBefore,
            ['/' . basename(__FILE__, '.php') . '/'],
            [],
            $renameOperation,
            $renameAfter,
            [],
        ];
        yield 'rename ignored by class' => [
            $path,
            $renameBefore,
            [],
            [self::class],
            $renameOperation,
            $renameAfter,
            [],
        ];

        $rmdirBefore = function (string $path) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        };
        $rmdirOperation = function (string $path) {
            rmdir($path);
        };
        $rmdirAfter = function (string $path) {
            if (is_dir($path)) {
                rmdir($path);
            }
        };

        yield 'rmdir matched' => [
            $path = __DIR__ . '/stub/dir-to-remove',
            $rmdirBefore,
            [],
            [],
            $rmdirOperation,
            $rmdirAfter,
            [
                'rmdir' => [
                    ['path' => $path, 'args' => ['options' => 8]], // 8 for some reasons
                ],
            ],
        ];
        yield 'rmdir ignored by path' => [
            $path,
            $rmdirBefore,
            ['/' . basename(__FILE__, '.php') . '/'],
            [],
            $rmdirOperation,
            $rmdirAfter,
            [],
        ];
        yield 'rmdir ignored by class' => [
            $path,
            $rmdirBefore,
            [],
            [self::class],
            $rmdirOperation,
            $rmdirAfter,
            [],
        ];

        $unlinkBefore = function (string $path) {
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            if (!is_file($path)) {
                touch($path);
            }
        };
        $unlinkOperation = function (string $path) {
            unlink($path);
        };
        $unlinkAfter = function (string $path) {
            FileHelper::removeDirectory(dirname($path));
        };

        yield 'unlink matched' => [
            $path = __DIR__ . '/stub/file-to-unlink.txt',
            $unlinkBefore,
            [],
            [],
            $unlinkOperation,
            $unlinkAfter,
            [
                'unlink' => [
                    ['path' => $path, 'args' => []],
                ],
            ],
        ];
        yield 'unlink ignored by path' => [
            $path,
            $unlinkBefore,
            ['/' . basename(__FILE__, '.php') . '/'],
            [],
            $unlinkOperation,
            $unlinkAfter,
            [],
        ];
        yield 'unlink ignored by class' => [
            $path,
            $unlinkBefore,
            [],
            [self::class],
            $unlinkOperation,
            $unlinkAfter,
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
