<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;

final class EventDispatcherInterfaceProxy implements EventDispatcherInterface
{
    private EventDispatcherInterface $dispatcher;
    private EventCollectorInterface $collector;

    public function __construct(EventDispatcherInterface $dispatcher, EventCollectorInterface $collector)
    {
        $this->dispatcher = $dispatcher;
        $this->collector = $collector;
    }

    public function dispatch(object $event)
    {
        $this->collector->collect($event);

        return $this->dispatcher->dispatch($event);
    }
}
