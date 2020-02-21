<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;

final class EventCollector implements CollectorInterface, EventDispatcherInterface
{
    use CollectorTrait;

    private array $events = [];

    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function collect(): array
    {
        return $this->events;
    }

    public function dispatch(object $event)
    {
        if ($this->isActive()) {
            $this->collectEvent($event);
        }

        return $this->dispatcher->dispatch($event);
    }

    private function collectEvent(object $event): void
    {
        $this->events[] = [
            'event' => get_class($event),
            'time' => microtime(true),
        ];
    }
}
