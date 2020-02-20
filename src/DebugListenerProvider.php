<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\Yii\Debug\Event\ApplicationShutdown;
use Yiisoft\Yii\Debug\Event\ApplicationStartup;

class DebugListenerProvider implements ListenerProviderInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $container = $this->container;
        if ($event instanceof ApplicationStartup) {
            yield function (ApplicationStartup $event) use ($container) {
                $container->get(Debugger::class)->startup();
            };
        }
        if ($event instanceof ApplicationShutdown) {
            yield function (ApplicationShutdown $event) use ($container) {
                $container->get(Debugger::class)->shutdown();
            };
        }
    }
}
