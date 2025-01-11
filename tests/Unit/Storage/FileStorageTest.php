<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Storage;

use PHPUnit\Framework\Attributes\DataProvider;
use Yiisoft\Files\FileHelper;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
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

        for ($i=1; $i<=10; $i++) {
            $storage->write('test'.$i, [['data']], [], []);
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

    public function getStorage(): FileStorage
    {
        return new FileStorage($this->path);
    }
}
