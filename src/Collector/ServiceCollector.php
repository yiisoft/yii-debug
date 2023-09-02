<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

final class ServiceCollector implements SummaryCollectorInterface
{
    use CollectorTrait;

    private array $items = [];

    public function __construct(private TimelineCollector $timelineCollector)
    {
    }

    public function getCollected(): array
    {
        if (!$this->isActive()) {
            return [];
        }
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
        $this->timelineCollector->collect(count($this->items), $this);
    }

    public function getSummary(): array
    {
        if (!$this->isActive()) {
            return [];
        }
        return [
            'service' => [
                'total' => count($this->items),
            ],
        ];
    }

    private function reset(): void
    {
        $this->items = [];
    }
}
