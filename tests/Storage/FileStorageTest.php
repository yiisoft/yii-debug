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
    private string $path = 'runtime';
    private Filesystem $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = new Filesystem(new LocalFilesystemAdapter('tests'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->fileSystem->deleteDirectory($this->path);
    }

    /**
     * @param array $data
     *
     * @dataProvider dataProvider()
     */
    public function testFlushWithGC(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $storage->setHistorySize(5);
        $collector = $this->createFakeCollector($data);

        $storage->addCollector($collector);
        $storage->flush();
        $this->assertLessThanOrEqual(5, count($storage->read()));
    }

    /**
     * @dataProvider dataProvider()
     *
     * @param array $data
     */
    public function testClear(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $collector = $this->createFakeCollector($data);

        $storage->addCollector($collector);
        $storage->flush();
        $storage->clear();
        $this->assertDirectoryDoesNotExist($this->path);
    }

    /**
     * @param DebuggerIdGenerator $idGenerator
     *
     * @return FileStorage|StorageInterface
     */
    public function getStorage(DebuggerIdGenerator $idGenerator): StorageInterface
    {
        return new FileStorage(
            $this->path,
            $this->fileSystem,
            $idGenerator,
            new Aliases()
        );
    }
}
