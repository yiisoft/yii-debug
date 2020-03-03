<?php

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\EventDispatcher\Provider\ConcreteProvider;
use Yiisoft\Di\ContainerProxyInterface;
use Yiisoft\Yii\Debug\Collector\CommonServiceCollector;
use Yiisoft\Yii\Debug\Collector\CommonServiceCollectorInterface;
use Yiisoft\Yii\Debug\DebugEventDispatcher;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\Storage\FileStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;

/**
 * @var $params array
 */

if (!(bool)($params['debugger.enabled'] ?? false)) {
    return [];
}

return [
    LogCollectorInterface::class => LogCollector::class,
    EventCollectorInterface::class => EventCollector::class,
    CommonServiceCollectorInterface::class => CommonServiceCollector::class,
    ContainerProxyInterface::class => ContainerProxy::class,
    ContainerProxyConfig::class => static function (ContainerInterface $container) use ($params) {
        $collector = $container->get(CommonServiceCollectorInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);
        $debuggerEnabled = (bool)($params['debugger.enabled'] ?? false);
        $trackedServices = (array)($params['debugger.trackedServices'] ?? []);
        $decoratedServices = (array)($params['container.decorators'] ?? []);
        $runtime = $container->get(Aliases::class)->get('@runtime');
        $path = "$runtime/cache/container-proxy";
        if (!is_dir($path) && !mkdir($path)) {
            throw new \RuntimeException("Proxy cache directory '$path' can not be created");
        }
        $logLevel = ContainerProxy::LOG_ARGUMENTS | ContainerProxy::LOG_RESULT | ContainerProxy::LOG_ERROR;
        return new ContainerProxyConfig(
            $debuggerEnabled,
            array_merge($trackedServices, $decoratedServices),
            $dispatcher,
            $collector,
            $path,
            $logLevel);
    },
    StorageInterface::class => function (ContainerInterface $container) {
        $runtime = $container->get(Aliases::class)->get('@runtime');
        $path = "$runtime/debug";
        $id = time();
        if (!is_dir($path) && !mkdir($path)) {
            throw new \RuntimeException("Debugger directory '$path' can not be created");
        }

        return new FileStorage("$path/$id.data");
    },
    Debugger::class => function (ContainerInterface $container) use ($params) {
        return new Debugger(
            $container->get(StorageInterface::class),
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
