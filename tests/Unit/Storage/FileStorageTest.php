<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Storage;

use Yiisoft\Files\FileHelper;
use Yiisoft\Yii\Debug\Storage\FileStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class FileStorageTest extends AbstractStorageTestCase
{
    private string $path = __DIR__ . '/runtime';

    protected function tearDown(): void
    {
        parent::tearDown();
        FileHelper::removeDirectory($this->path);
    }

    public function testFlushWithGC(): void
    {
        $storage = $this->getStorage();
        $storage->setHistorySize(5);

        for ($i = 1; $i <= 10; $i++) {
            $storage->write('test' . $i, [['data']], [], []);
        }

        $this->assertCount(5, $storage->read(StorageInterface::TYPE_SUMMARY, null));
    }

    public function testHistorySize(): void
    {
        $storage = $this->getStorage();
        $storage->setHistorySize(2);

        $storage->write('test1', [['data']], [], []);
        $storage->write('test2', [['data']], [], []);
        $storage->write('test3', [['data']], [], []);

        $summary = $storage->read(StorageInterface::TYPE_SUMMARY);
        $this->assertCount(2, $summary);
    }

    public function testClear(): void
    {
        $storage = $this->getStorage();
        $storage->write('test1', [['data']], [], []);
        $storage->clear();
        $this->assertDirectoryDoesNotExist($this->path);
    }

    public function testConcurrentDeletion(): void
    {
        $storage = $this->getStorage();
        $storage->setHistorySize(10);

        // Write some data
        for ($i = 1; $i <= 5; $i++) {
            $storage->write('test' . $i, [['data' . $i]], [], ['id' => 'test' . $i]);
            usleep(1000); // 1ms delay to ensure different modification times
        }

        // Find all summary files
        $pattern = $this->path . '/**/**/summary.json';
        $summaryFiles = glob($pattern, GLOB_NOSORT);
        $this->assertNotEmpty($summaryFiles, 'Should have summary files');

        // Delete one summary file to simulate concurrent deletion during gc
        if (!empty($summaryFiles)) {
            unlink($summaryFiles[0]);
        }

        // This should not produce any warnings even though a file was deleted
        $summary = $storage->read(StorageInterface::TYPE_SUMMARY);

        // We should get 4 results (5 written - 1 deleted)
        $this->assertCount(4, $summary);
    }

    public function getStorage(): FileStorage
    {
        return new FileStorage($this->path);
    }
}
