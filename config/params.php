<?php

declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Assets\AssetLoaderInterface;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Debug\Collector\AssetCollector;
use Yiisoft\Yii\Debug\Collector\CommandCollector;
use Yiisoft\Yii\Debug\Collector\ConsoleAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Collector\RouterCollector;
use Yiisoft\Yii\Debug\Collector\RouterCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollectorInterface;
use Yiisoft\Yii\Debug\Collector\ValidatorCollectorInterface;
use Yiisoft\Yii\Debug\Collector\WebAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\WebViewCollector;
use Yiisoft\Yii\Debug\Command\ResetCommand;
use Yiisoft\Yii\Debug\Proxy\AssetLoaderInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\UrlMatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\ValidatorInterfaceProxy;

/**
 * @var $params array
 */

return [
    'yiisoft/yii-debug' => [
        'enabled' => true,
        'collectors' => [
            LogCollectorInterface::class,
            EventCollectorInterface::class,
            ServiceCollectorInterface::class,
            ValidatorCollectorInterface::class,
        ],
        'collectors.web' => [
            WebAppInfoCollector::class,
            RequestCollector::class,
            RouterCollector::class,
            MiddlewareCollector::class,
            AssetCollector::class,
            WebViewCollector::class,
        ],
        'collectors.console' => [
            ConsoleAppInfoCollector::class,
            CommandCollector::class,
        ],
        'trackedServices' => [
            LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollectorInterface::class],
            ValidatorInterface::class => [ValidatorInterfaceProxy::class, ValidatorCollectorInterface::class],
            EventDispatcherInterface::class => [EventDispatcherInterfaceProxy::class, EventCollectorInterface::class],
            UrlMatcherInterface::class => [UrlMatcherInterfaceProxy::class, RouterCollectorInterface::class],
            AssetLoaderInterface::class => [AssetLoaderInterfaceProxy::class, AssetCollector::class],
            CacheInterface::class,
        ],
        'dumper.excludedClasses' => [
            'PhpParser\\Parser\\Php7',
            'PhpParser\\NodeTraverser',
            'PhpParser\\NodeVisitor\\NameResolver',
            'PhpParser\\NameContext',
            'PhpParser\\Node\\Name',
            'PhpParser\\ErrorHandler\\Throwing',
            'Spiral\\Attributes\\Internal\\AttributeParser',
            'Doctrine\\Inflector\\Rules\\Pattern',
            'Doctrine\\Inflector\\Rules\\Word',
            'Doctrine\\Inflector\\Rules\\Substitution',
            'Doctrine\\Inflector\\Rules\\Transformation',
        ],
        'logLevel' => ContainerInterfaceProxy::LOG_ARGUMENTS | ContainerInterfaceProxy::LOG_RESULT | ContainerInterfaceProxy::LOG_ERROR,
        'path' => '@runtime/debug',
        'optionalRequests' => [
            '/assets/*',
        ],
    ],
    'yiisoft/yii-console' => [
        'commands' => [
            'debug/reset' => ResetCommand::class,
        ],
    ],
];
