<?php

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Container\Proxy\ContainerProxyInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Collector\ServiceCollectorInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\Storage\FileStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Filesystem\FilesystemInterface;

/**
 * @var $params array
 */

if (!(bool)($params['debugger.enabled'] ?? false)) {
    return [];
}

return [
    LogCollectorInterface::class => LogCollector::class,
    EventCollectorInterface::class => EventCollector::class,
    ServiceCollectorInterface::class => ServiceCollector::class,
    ContainerProxyInterface::class => ContainerProxy::class,
    ContainerProxyConfig::class => static function (ContainerInterface $container) use ($params) {
        $collector = $container->get(ServiceCollectorInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);
        $debuggerEnabled = (bool)($params['debugger.enabled'] ?? false);
        $trackedServices = (array)($params['debugger.trackedServices'] ?? []);
        $path = $container->get(Aliases::class)->get('@runtime/cache/container-proxy');
        $logLevel = $params['debugger.logLevel'] ?? 0;
        return new ContainerProxyConfig(
            $debuggerEnabled,
            $trackedServices,
            $dispatcher,
            $collector,
            $path,
            $logLevel
        );
    },
    StorageInterface::class => function (ContainerInterface $container) use ($params) {
        $filesystem = $container->get(FilesystemInterface::class);
        return new FileStorage($params['debugger.path'], $filesystem, $container->get(DebuggerIdGenerator::class));
    },
    Debugger::class => static function (ContainerInterface $container) use ($params) {
        return new Debugger(
            $container->get(DebuggerIdGenerator::class),
            $container->get(StorageInterface::class),
            array_map(
                fn ($class) => $container->get($class),
                $params['debugger.collectors']
            )
        );
    },
];
