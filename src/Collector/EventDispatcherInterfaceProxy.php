<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherInterfaceProxy implements EventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private EventCollector $collector
    ) {
    }

    public function dispatch(object $event): object
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect($event, $callStack['file'] . ':' . $callStack['line']);

        return $this->dispatcher->dispatch($event);
    }
}
