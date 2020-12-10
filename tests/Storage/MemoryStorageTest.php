<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Storage;

use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class MemoryStorageTest extends AbstractStorageTest
{
    public function getStorage(DebuggerIdGenerator $idGenerator): StorageInterface
    {
        return new MemoryStorage($idGenerator);
    }
}
