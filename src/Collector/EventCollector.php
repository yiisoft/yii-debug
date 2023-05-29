<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use ReflectionClass;
use Yiisoft\Yii\Console\Event\ApplicationStartup as ConsoleApplicationStartup;
use Yiisoft\Yii\Http\Event\ApplicationStartup as HttpApplicationStartup;

class EventCollector implements SummaryCollectorInterface
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

    public function collect(object $event, string $line): void
    {
        if (
            !$event instanceof HttpApplicationStartup
            && !$event instanceof ConsoleApplicationStartup
            && !$this->isActive()
        ) {
            return;
        }

        $this->collectEvent($event, $line);
    }

    private function collectEvent(object $event, $line): void
    {
        $this->events[] = [
            'name' => $event::class,
            'event' => $event,
            'file' => (new ReflectionClass($event))->getFileName(),
            'line' => $line,
            'time' => microtime(true),
        ];
    }

    public function getSummary(): array
    {
        if (!$this->isActive()) {
            return [];
        }
        return [
            'event' => [
                'total' => count($this->events),
            ],
        ];
    }

    private function reset(): void
    {
        $this->events = [];
    }
}
