<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

final class TimelineCollector implements CollectorInterface
{
    use CollectorTrait;

    private array $events = [];

    public function getCollected(): array
    {
        if (!$this->isActive()) {
            return [];
        }
        return $this->events;
    }

    public function collect(string|int $reference, CollectorInterface $collector): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->events[] = [
            'reference' => $reference,
            'collector' => $collector::class,
            'time' => time()
        ];
    }

    private function reset(): void
    {
        $this->events = [];
    }
}
