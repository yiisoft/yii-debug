<?php

namespace Yiisoft\Yii\Debug\Dispatcher;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Web\Event\AfterRequest;

final class DebugShutdownDispatcher implements EventDispatcherInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function dispatch(object $event)
    {
        if ($event instanceof AfterRequest) {
            $this->container->get(Debugger::class)->shutdown();
        }

        return $event;
    }
}
