<?php

namespace Yiisoft\Yii\Debug\Tests\Storage;

use Yiisoft\Yii\Debug\Storage\MemStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

class MemTargetTest extends AbstractTargetTest
{
    public function getTarget(): StorageInterface
    {
        return new MemStorage();
    }
}
