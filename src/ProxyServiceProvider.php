<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Contracts\ServiceProviderInterface;
use Yiisoft\Yii\Debug\Proxy\ContainerInterfaceProxy;
use Yiisoft\Yii\Debug\Proxy\ContainerProxyConfig;

final class ProxyServiceProvider implements ServiceProviderInterface
{
    /**
     * @psalm-suppress InaccessibleMethod
     */
    public function getDefinitions(): array
    {
        return [
            ContainerInterface::class => static function (ContainerInterface $container) {
                return new ContainerInterfaceProxy($container, $container->get(ContainerProxyConfig::class));
            }
        ];
    }

    public function getExtensions(): array
    {
        return [];
    }
}
