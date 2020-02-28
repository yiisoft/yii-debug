<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;
use Yiisoft\Yii\Web\Event\ApplicationStartup;

final class EventDispatcherProxy implements EventDispatcherInterface
{
    private EventDispatcherInterface $dispatcher;
    private EventDispatcherInterface $debugEventDispatcher;
    private EventCollectorInterface $collector;
    private Debugger $debugger;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        EventDispatcherInterface $debugEventDispatcher,
        EventCollectorInterface $collector,
        Debugger $debugger
    ) {
        $this->dispatcher = $dispatcher;
        $this->debugEventDispatcher = $debugEventDispatcher;
        $this->collector = $collector;
        $this->debugger =  $debugger;
    }

    public function dispatch(object $event)
    {
        $this->collector->collect($event);
        $this->processEvent($event);
        $this->debugEventDispatcher->dispatch($event);

        return $this->dispatcher->dispatch($event);
    }

    private function processEvent(object $event)
    {
        if ($event instanceof ApplicationStartup) {
            $this->debugger->startup();
        }

        if ($event instanceof ApplicationShutdown) {
            $this->debugger->shutdown();
        }
    }
}
