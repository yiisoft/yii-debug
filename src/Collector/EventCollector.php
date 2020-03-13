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
            'event' => get_class($event),
            'time' => microtime(true),
        ];
    }
}
