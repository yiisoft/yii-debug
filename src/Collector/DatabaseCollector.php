<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

class DatabaseCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $queries = [];

    public function getCollected(): array
    {
        return $this->queries;
    }

    public function collect(string $sql, array $params, string $line): void
    {
        $this->collectQuery($sql, $params, $line);
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
            'db' => [
                'total' => count($this->queries),
            ],
        ];
    }

    private function reset(): void
    {
        $this->queries = [];
    }
}
