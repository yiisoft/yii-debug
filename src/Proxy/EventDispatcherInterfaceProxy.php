<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;

class EventDispatcherInterfaceProxy implements EventDispatcherInterface
{
    protected EventDispatcherInterface $dispatcher;
    protected EventCollectorInterface $collector;

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
