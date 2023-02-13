<?php

declare(strict_types=1);

use Cycle\ORM\ORMInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Assets\AssetLoaderInterface;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Injector\Injector;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Debug\Collector\CommandCollector;
use Yiisoft\Yii\Debug\Collector\ConsoleAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Database\ConnectionInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Database\CycleCollector;
use Yiisoft\Yii\Debug\Collector\Database\CycleORMInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Database\DatabaseCollector;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\HttpClientCollector;
use Yiisoft\Yii\Debug\Collector\HttpClientInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\LoggerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Queue\QueueCollector;
use Yiisoft\Yii\Debug\Collector\Queue\QueueFactoryInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Queue\QueueWorkerInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\ServiceCollector;
use Yiisoft\Yii\Debug\Collector\Stream\FilesystemStreamCollector;
use Yiisoft\Yii\Debug\Collector\Stream\HttpStreamCollector;
use Yiisoft\Yii\Debug\Collector\ValidatorCollector;
use Yiisoft\Yii\Debug\Collector\ValidatorInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Web\AssetCollector;
use Yiisoft\Yii\Debug\Collector\Web\AssetLoaderInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Web\MiddlewareCollector;
use Yiisoft\Yii\Debug\Collector\Web\RequestCollector;
use Yiisoft\Yii\Debug\Collector\Web\RouterCollector;
use Yiisoft\Yii\Debug\Collector\Web\UrlMatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\Web\WebAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\Web\WebViewCollector;
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
            DatabaseCollector::class,
            ServiceCollector::class,
            ValidatorCollector::class,
            QueueCollector::class,
            HttpClientCollector::class,
            FilesystemStreamCollector::class,
            HttpStreamCollector::class,
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
            ConnectionInterface::class => [ConnectionInterfaceProxy::class, DatabaseCollector::class],
            QueueFactoryInterface::class => [QueueFactoryInterfaceProxy::class, QueueCollector::class],
            WorkerInterface::class => [QueueWorkerInterfaceProxy::class, QueueCollector::class],
            UrlMatcherInterface::class => [UrlMatcherInterfaceProxy::class, RouterCollector::class],
            ValidatorInterface::class => [ValidatorInterfaceProxy::class, ValidatorCollector::class],
            AssetLoaderInterface::class => [AssetLoaderInterfaceProxy::class, AssetCollector::class],
            ClientInterface::class => [HttpClientInterfaceProxy::class, HttpClientCollector::class],
            ORMInterface::class => [CycleORMInterfaceProxy::class, CycleCollector::class],
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
