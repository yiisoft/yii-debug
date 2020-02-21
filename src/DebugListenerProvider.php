<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;
use Yiisoft\Yii\Web\Event\ApplicationStartup;

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
            yield function () use ($container) {
                $container->get(Debugger::class)->startup();
            };
        } elseif ($event instanceof ApplicationShutdown) {
            yield function () use ($container) {
                $container->get(Debugger::class)->shutdown();
            };
        }
    }
}
