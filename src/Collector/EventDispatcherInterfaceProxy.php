<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherInterfaceProxy implements EventDispatcherInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly EventCollector $collector
    ) {
    }

    public function dispatch(object $event): object
    {
        /** @psalm-var array{file: string, line: int} $callStack */
        $callStack = debug_backtrace()[0];

        $this->collector->collect($event, $callStack['file'] . ':' . $callStack['line']);

        return $this->dispatcher->dispatch($event);
    }
}
