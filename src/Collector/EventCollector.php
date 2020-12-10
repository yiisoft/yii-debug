<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Console\Event\ApplicationStartup as ConsoleApplicationStartup;
use Yiisoft\Yii\Web\Event\ApplicationStartup as WebApplicationStartup;
use function get_class;

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
        if (!$event instanceof WebApplicationStartup && !$event instanceof ConsoleApplicationStartup && !$this->isActive()) {
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
