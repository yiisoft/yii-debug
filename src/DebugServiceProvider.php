<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Yii\Debug\Storage\FileStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Filesystem\FilesystemInterface;

final class DebugServiceProvider extends ServiceProvider
{
    public function register(Container $container): void
    {
        $debugSessionId = 'yii-debug-' . microtime(true);

        $container->setMultiple([
            StorageInterface::class => function (ContainerInterface $container) use ($debugSessionId) {
                $params = $container->get('params');
                $filesystem = $container->get(FilesystemInterface::class);
                $path = $params['debugger.path'] . DIRECTORY_SEPARATOR . $debugSessionId . '.data';

                return new FileStorage($path, $filesystem);
            },
            Debugger::class => static function (ContainerInterface $container) use ($debugSessionId) {
                $params = $container->get('params');

                return new Debugger(
                    $debugSessionId,
                    $container->get(StorageInterface::class),
                    array_map(
                        fn($class) => $container->get($class),
                        $params['debugger.collectors']
                    )
                );
            },
        ]);
    }
}
