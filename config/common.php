<?php

use Psr\Container\ContainerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ConcreteProvider;
use Yiisoft\Yii\Debug\DebugEventDispatcher;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Target\FileTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

/**
 * @var $params array
 */

if (!(bool)($params['debugger.enabled'] ?? false)) {
    return [];
}

return [
    TargetInterface::class => function (ContainerInterface $container) {
        $runtime = $container->get(Aliases::class)->get('@runtime');
        $id = time();

        return new FileTarget("$runtime/debug/$id.data");
    },
    Debugger::class => function (ContainerInterface $container) use ($params) {
        return new Debugger(
            $container->get(TargetInterface::class),
            array_map(
                fn ($class) => $container->get($class),
                $params['debugger.collectors']
            )
        );
    },
    DebugEventDispatcher::class => function (ContainerInterface $container) use ($params) {
        $provider = new ConcreteProvider();
        foreach ($params['debugger.event_handlers'] as $event => $eventHandlers) {
            foreach ($eventHandlers as $eventHandler) {
                $provider->attach($event, $eventHandler($container));
            }
        }

        return new Dispatcher($provider);
    },
];
