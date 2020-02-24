<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

interface StorageInterface
{
    public function persist(CollectorInterface $collector): void;

    public function getData(): array;

    public function flush(): void;
}
