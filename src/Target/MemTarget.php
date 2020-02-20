<?php

namespace Yiisoft\Yii\Debug\Target;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class MemTarget implements TargetInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    public function persist(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function getData(): array
    {
        $data = [];

        foreach ($this->collectors as $collector) {
            $data[] = $collector->collect();
        }

        return $data;
    }

    public function flush(): void
    {
    }
}
