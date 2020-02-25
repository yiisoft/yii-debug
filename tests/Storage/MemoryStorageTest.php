<?php

namespace Yiisoft\Yii\Debug\Tests\Storage;

use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

class MemoryStorageTest extends AbstractStorageTest
{
    public function getTarget(): StorageInterface
    {
        return new MemoryStorage();
    }
}
