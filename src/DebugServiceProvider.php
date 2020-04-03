<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;
use Yiisoft\Yii\Web\Config\EventConfigurator;

final class DebugServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        $eventConfigurator = $container->get(EventConfigurator::class);
        $eventConfigurator->registerListeners(require dirname(__DIR__) . '/config/events.php');
        $logger = $container->get(LoggerInterface::class);
        $dispatcher = $container->get(EventDispatcherInterface::class);

        $container->setMultiple(
            [
                // interfaces overriding
                LoggerInterface::class => static function (ContainerInterface $container) use ($logger) {
                    return new LoggerInterfaceProxy($logger, $container->get(LogCollector::class));
                },
                EventDispatcherInterface::class => static function (ContainerInterface $container) use ($dispatcher) {
                    return new EventDispatcherInterfaceProxy($dispatcher, $container->get(EventCollector::class));
                },
            ]
        );
    }
}
