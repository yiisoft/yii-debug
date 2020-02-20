<?php

namespace Yiisoft\Yii\Debug\Target;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class MemTarget implements TargetInterface
{
    private array $collectors = [];
    private array $data = [];

    public function persist(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function getData(): array
    {
        return $this->data = [];
    }

    public function flush(): void
    {
        // TODO make export to storage
        $this->data = [];
    }
}
