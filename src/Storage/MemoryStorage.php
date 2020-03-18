<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;

final class MemoryStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    private ?string $debugId = null;

    public function __construct(DebuggerIdGenerator $idGenerator)
    {
        $this->debugId = $idGenerator->getId();
    }

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

    public function setDebugId(string $id): void
    {
        if ($this->debugId === null) {
            $this->debugId = $id;
        }
    }

    public function flush(): void
    {
        $this->collectors = [];
    }
}
