<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;

final class MemoryStorage implements StorageInterface
{
    private DebuggerIdGenerator $idGenerator;
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    public function __construct(DebuggerIdGenerator $idGenerator)
    {
        $this->idGenerator = $idGenerator;
    }

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function read($type = self::TYPE_INDEX): array
    {
        return [$this->idGenerator->getId() => $this->getData()];
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
