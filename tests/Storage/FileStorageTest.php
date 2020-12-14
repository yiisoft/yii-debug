<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Storage;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\FileStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Filesystem\Filesystem;

final class FileStorageTest extends AbstractStorageTest
{
    private string $path = 'runtime/debug';
    protected function tearDown(): void
    {
        parent::tearDown();
        //rmdir($this->path);
    }

    /**
     * @param DebuggerIdGenerator $idGenerator
     *
     * @return StorageInterface
     */
    public function getStorage(DebuggerIdGenerator $idGenerator): StorageInterface
    {
        return new FileStorage($this->path, new Filesystem(new LocalFilesystemAdapter('tests')), $idGenerator, new Aliases());
    }
}
