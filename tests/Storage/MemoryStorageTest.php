<?php

namespace Yiisoft\Yii\Debug\Tests\Storage;

use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class MemoryStorageTest extends AbstractStorageTest
{
    public function getStorage(): StorageInterface
    {
        return new MemoryStorage();
    }
}
