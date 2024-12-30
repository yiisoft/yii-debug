<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Storage;

final class MemoryStorage implements StorageInterface
{
    /**
     * @psalm-var array<string, array{data: array, summary: array, objects: array}>
     */
    private array $storage = [];

    public function read(string $type, ?string $id = null): array
    {
        return $this->storage[$id][$type] ?? [];
    }

    public function write(string $id, array $data, array $summary): void
    {
        $this->storage[$id] = [
            'data' => $data,
            'summary' => $summary,
            'objects' => array_merge(...array_values($data)),
        ];
    }

    public function clear(): void
    {
        $this->storage = [];
    }
}
