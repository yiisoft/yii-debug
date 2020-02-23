<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

final class EventDispatcherProxy implements EventDispatcherInterface
{
    private EventDispatcherInterface $dispatcher;
    private CollectorInterface $collector;

    public function __construct(EventDispatcherInterface $dispatcher, CollectorInterface $collector)
    {
        $this->dispatcher = $dispatcher;
        $this->collector = $collector;
    }

    public function dispatch(object $event)
    {
        $this->collector->dispatch($event);

        return $this->dispatcher->dispatch($event);
    }
}
