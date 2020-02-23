<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\ApplicationStartup;

final class EventCollector implements CollectorInterface
{
    use CollectorTrait;

    private array $events = [];

    public function collect(): array
    {
        return $this->events;
    }

    public function dispatch(...$payload): void
    {
        $event = current($payload);
        if (!is_object($event) || (!$this->isActive() && !$event instanceof ApplicationStartup)) {
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
