<?php

namespace Yiisoft\Yii\Debug\Target;

class MemTarget implements TargetInterface
{
    private array $data = [];

    public function add(...$args): void
    {
        $this->data = [...$args];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function flush(): void
    {
        // TODO make export to storage
        $this->data = [];
    }
}
