<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Assets\AssetLoaderInterface;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Injector\Injector;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Debug\Collector\AssetCollector;
use Yiisoft\Yii\Debug\Collector\AssetLoaderInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\CommandCollector;
use Yiisoft\Yii\Debug\Collector\ConsoleAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\LoggerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\QueueCollector;
use Yiisoft\Yii\Debug\Collector\QueueFactoryInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\QueueWorkerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Collector\RouterCollector;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Collector\UrlMatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\ValidatorCollector;
use Yiisoft\Yii\Debug\Collector\ValidatorInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\WebAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\WebViewCollector;
use Yiisoft\Yii\Debug\Command\ResetCommand;
use Yiisoft\Yii\Queue\QueueFactoryInterface;
use Yiisoft\Yii\Queue\Worker\WorkerInterface;

/**
 * @var $params array
 */

return [
    'yiisoft/yii-debug' => [
        'enabled' => true,
        'collectors' => [
            LogCollector::class,
            EventCollector::class,
            ServiceCollector::class,
            ValidatorCollector::class,
            QueueCollector::class,
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
            Injector::class => fn (ContainerInterface $container) => new Injector($container),
            LoggerInterface::class => [LoggerInterfaceProxy::class, LogCollector::class],
            EventDispatcherInterface::class => [EventDispatcherInterfaceProxy::class, EventCollector::class],
            QueueFactoryInterface::class => [QueueFactoryInterfaceProxy::class, QueueCollector::class],
            WorkerInterface::class => [QueueWorkerInterfaceProxy::class, QueueCollector::class],
            UrlMatcherInterface::class => [UrlMatcherInterfaceProxy::class, RouterCollector::class],
            ValidatorInterface::class => [ValidatorInterfaceProxy::class, ValidatorCollector::class],
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
        'ignoredRequests' => [
            '/assets/*',
        ],
        'ignoredCommands' => [
            'completion',
            'help',
            'list',
            'serve',
            'debug/reset',
        ],
    ],
    'yiisoft/yii-console' => [
        'commands' => [
            'debug/reset' => ResetCommand::class,
        ],
    ],
];
