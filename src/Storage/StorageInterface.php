<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

/**
 * Debug data storage responsibility is to store debug data from collectors added
 */
interface StorageInterface
{
    public const TYPE_INDEX = 'index';
    public const TYPE_DATA = 'data';
    public const TYPE_OBJECTS = 'objects';
    /**
     * Add collector to get debug data from
     * @param CollectorInterface $collector
     */
    public function addCollector(CollectorInterface $collector): void;

    public function getData(): array;

    /**
     * Read data from storage
     * @param string $type
     * @return array
     */
    public function read(string $type = self::TYPE_INDEX): array;

    /**
     * Flush data from collectors into storage
     */
    public function flush(): void;
}
