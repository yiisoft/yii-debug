<?php

namespace Yiisoft\Yii\Debug\Dispatcher;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Web\Event\ApplicationStartup;

class DebugStartupDispatcher implements EventDispatcherInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function dispatch(object $event)
    {
        if ($event instanceof ApplicationStartup) {
            $this->container->get(Debugger::class)->startup();
        }

        return $event;
    }
}
