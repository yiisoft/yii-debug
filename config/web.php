<?php

use Psr\Container\ContainerInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Target\FileTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

/**
 * @var $params array
 */

return [
    TargetInterface::class => function (ContainerInterface $container) {
        $runtime = $container->get(Aliases::class)->get('@runtime');
        $id = time();

        return new FileTarget("$runtime/debug/$id.data");
    },
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
