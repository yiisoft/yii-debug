<?php

use Psr\Container\ContainerInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Target\TargetInterface;

/**
 * @var $params array
 */

return [
    Debugger::class => function (ContainerInterface $container) use ($params) {
        return new Debugger(
            $container->get(TargetInterface::class),
            array_map(
                fn($class) => $container->get($class),
                $params['debugger.collectors']
            )
        );
    },
];
