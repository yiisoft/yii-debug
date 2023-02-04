<?php

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\VarDumper\ClosureExporter;
use Yiisoft\VarDumper\UseStatementParser;
use Yiisoft\Yii\Debug\Collector\ContainerProxyConfig;
use Yiisoft\Yii\Debug\Collector\FileStreamCollector;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\FileStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

/**
 * @var $params array
 */

$common = [
    StorageInterface::class => static function (ContainerInterface $container) use ($params) {
        $params = $params['yiisoft/yii-debug'];
        $debuggerIdGenerator = $container->get(DebuggerIdGenerator::class);
        $aliases = $container->get(Aliases::class);
        $excludedClasses = $params['dumper.excludedClasses'];
        $fileStorage = new FileStorage($params['path'], $debuggerIdGenerator, $aliases, $excludedClasses);
        if (isset($params['historySize'])) {
            $fileStorage->setHistorySize((int) $params['historySize']);
        }
        return $fileStorage;
    },
];

if (!(bool) ($params['yiisoft/yii-debug']['enabled'] ?? false)) {
    return $common;
}

return array_merge([
    ContainerProxyConfig::class => static function (ContainerInterface $container) use ($params) {
        $params = $params['yiisoft/yii-debug'];
        $collector = $container->get(ServiceCollector::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);
        $debuggerEnabled = (bool) ($params['enabled'] ?? false);
        $trackedServices = (array) ($params['trackedServices'] ?? []);
        $path = $container->get(Aliases::class)->get('@runtime/cache/container-proxy');
        $logLevel = $params['logLevel'] ?? 0;
        return new ContainerProxyConfig(
            $debuggerEnabled,
            $trackedServices,
            $dispatcher,
            $collector,
            $path,
            $logLevel
        );
    },
    FileStreamCollector::class => [
        '__construct()' => [
            'ignoredPathPatterns' => [
                //'/ClosureExporter/',
                //'/UseStatementParser/',
                //'/' . preg_quote('yii-debug/src/Dumper', '/') . '/',
            ],
            'ignoredClasses' => [
                ClosureExporter::class,
                UseStatementParser::class,
                FileStorage::class,
                ClassLoader::class,
            ],
        ],
    ],
], $common);
