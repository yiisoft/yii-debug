<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yiisoft\VarDumper\Handler\CompositeHandler;
use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Debug\Collector\VarDumperCollector;
use Yiisoft\Yii\Debug\Collector\VarDumperHandlerInterfaceProxy;
use Yiisoft\Yii\Debug\DevServer\VarDumperHandler;

/**
 * @var $params array
 */

return [
    static function (ContainerInterface $container) use ($params) {
        if (!($params['yiisoft/yii-debug']['enabled'] ?? false)) {
            return;
        }
        if (!$container->has(VarDumperCollector::class)) {
            return;
        }

        // todo: remove VarDumperHandler if dev-server is not enabled
        VarDumper::setDefaultHandler(
            new VarDumperHandlerInterfaceProxy(
                new CompositeHandler([
                    VarDumper::getDefaultHandler(),
                    new VarDumperHandler(),
                ]),
                $container->get(VarDumperCollector::class),
            ),
        );
    },
];
