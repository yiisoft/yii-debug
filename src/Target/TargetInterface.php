<?php

namespace Yiisoft\Yii\Debug\Target;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

interface TargetInterface
{
    public function persist(CollectorInterface $collector): void;

    public function getData(): array;

    public function flush(): void;
}
