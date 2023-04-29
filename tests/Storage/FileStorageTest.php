<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Storage;

use Yiisoft\Aliases\Aliases;
use Yiisoft\Files\FileHelper;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\FileStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class FileStorageTest extends AbstractStorageTest
{
    private string $path = __DIR__ . '/runtime';

    protected function tearDown(): void
    {
        parent::tearDown();
        FileHelper::removeDirectory($this->path);
    }

    /**
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
        $this->assertLessThanOrEqual(5, count($storage->read(StorageInterface::TYPE_SUMMARY)));
    }

    /**
     * @dataProvider dataProvider()
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

    public function getStorage(DebuggerIdGenerator $idGenerator): StorageInterface
    {
        return new FileStorage(
            $this->path,
            $idGenerator,
            new Aliases()
        );
    }
}
