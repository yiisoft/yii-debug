<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;

final class MemoryStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    public function __construct(private DebuggerIdGenerator $idGenerator)
    {
    }

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    public function read(string $type, ?string $id = null): array
    {
        if ($type === self::TYPE_SUMMARY) {
            return [
                $this->idGenerator->getId() => [
                    'id' => $this->idGenerator->getId(),
                    'collectors' => array_keys($this->collectors),
                ],
            ];
        }

        return [$this->idGenerator->getId() => $this->getData()];
    }

    public function getData(): array
    {
        $data = [];

        foreach ($this->collectors as $name => $collector) {
            $data[$name] = $collector->getCollected();
        }

        return $data;
    }

    public function flush(): void
    {
        $this->collectors = [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function clear(): void
    {
    }
}
