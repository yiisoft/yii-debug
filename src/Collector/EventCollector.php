<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\ApplicationStartup;

final class EventCollector implements EventCollectorInterface
{
    use CollectorTrait;

    private array $events = [];

    public function getCollected(): array
    {
        return $this->events;
    }

    public function collect(object $event): void
    {
        if (!$this->isActive() && !$event instanceof ApplicationStartup) {
            return;
        }

        $this->collectEvent($event);
    }

    private function collectEvent(object $event): void
    {
        $this->events[] = [
            'name' => get_class($event),
            'event' => $event,
            'time' => microtime(true),
        ];
    }

    private function reset(): void
    {
        $this->events = [];
    }
}
