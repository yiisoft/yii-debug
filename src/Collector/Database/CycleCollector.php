<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Database;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

final class CycleCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $queries = [];

    public function getCollected(): array
    {
        return $this->queries;
    }

    public function collect(...$args): void
    {
        $this->queries[] = $args;
    }

    private function collectQuery(string $sql, array $params, string $line): void
    {
        $this->queries[] = [
            'rawSql' => $sql,
            'params' => $params,
            'line' => $line,
            'time' => microtime(true),
        ];
    }

    public function getIndexData(): array
    {
        return [
            'cycle' => [
                'total' => count($this->queries),
            ],
        ];
    }

    private function reset(): void
    {
        $this->queries = [];
    }
}
