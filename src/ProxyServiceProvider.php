<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Yii\Debug\Proxy\ContainerProxy;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;
use Yiisoft\Yii\Web\Config\EventConfigurator;

final class ProxyServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        $eventConfigurator = $container->get(EventConfigurator::class);
        $eventConfigurator->registerListeners(require dirname(__DIR__) . '/config/events.php');
        $container->set(ContainerInterface::class, static function (ContainerInterface $container) {
            return new ContainerProxy($container, $container->get(ContainerProxyConfig::class));
        });
    }
}
