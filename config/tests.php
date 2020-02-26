<?php

use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

return [
    StorageInterface::class => MemoryStorage::class,
];
