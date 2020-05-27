<?php

use Psr\Container\ContainerInterface;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Debugger;

if (!(bool)($params['yiisoft/yii-debugger']['enabled'] ?? false)) {
    return [];
}

return [
    Debugger::class => static function (ContainerInterface $container) use ($params) {
        $params = $params['yiisoft/yii-debugger'];
        return new Debugger(
            $container->get(DebuggerIdGenerator::class),
            $container->get(StorageInterface::class),
            array_map(
                fn ($class) => $container->get($class),
                array_merge($params['collectors'], $params['collectors.web'] ?? [])
            )
        );
    },
];
