<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Storage;

use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class MemoryStorageTest extends AbstractStorageTestCase
{
    public function getStorage(): StorageInterface
    {
        return new MemoryStorage();
    }
}
