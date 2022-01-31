<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;

final class ServiceCollector implements ServiceCollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $items = [];

    public function getCollected(): array
    {
        return $this->items;
    }

    public function collect(
        string $service,
        string $class,
        string $method,
        ?array $arguments,
        $result,
        string $status,
        ?object $error,
        float $timeStart,
        float $timeEnd
    ): void {
        if (!$this->isActive()) {
            return;
        }

        $this->items[] = [
            'service' => $service,
            'class' => $class,
            'method' => $method,
            'arguments' => $arguments,
            'result' => $result,
            'status' => $status,
            'error' => $error,
            'timeStart' => $timeStart,
            'timeEnd' => $timeEnd,
        ];
    }

    #[ArrayShape(['totalServices' => 'int|void'])]
    public function getIndexData(): array
    {
        return [
            'totalServices' => count($this->items),
        ];
    }

    private function reset(): void
    {
        $this->items = [];
    }
}
