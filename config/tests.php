<?php

return [
    \Yiisoft\Yii\Debug\Target\TargetInterface::class=>\Yiisoft\Yii\Debug\Target\MemTarget::class,
    \Psr\Log\LoggerInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return new \Yiisoft\Yii\Debug\Collector\LogCollector(
            $container->get(\Yiisoft\Yii\Debug\Target\TargetInterface::class),
            $container->get(\Yiisoft\Log\Logger::class)
        );
    },
];
