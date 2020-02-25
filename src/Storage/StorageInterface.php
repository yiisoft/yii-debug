<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

interface StorageInterface
{
    public function addCollector(CollectorInterface $collector): void;

    public function getData(): array;

    public function flush(): void;
}
