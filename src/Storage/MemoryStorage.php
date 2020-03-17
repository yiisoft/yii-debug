<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

final class MemoryStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function getData(): array
    {
        $data = [];

        foreach ($this->collectors as $collector) {
            $data[] = $collector->getCollected();
        }

        return $data;
    }

    public function flush(): void
    {
        $this->collectors = [];
    }
}
