<?php

namespace Yiisoft\Yii\Debug\Target;

interface TargetInterface
{
    public function add(...$args): void;

    public function getData(): array;

    public function flush(): void;
}
