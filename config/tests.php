<?php

return [
    \Yiisoft\Yii\Debug\Target\TargetInterface::class => \Yiisoft\Yii\Debug\Target\MemTarget::class,
    \Psr\Log\LoggerInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return new \Yiisoft\Yii\Debug\Collector\LogCollector(
            $container->get(\Yiisoft\Log\Logger::class)
        );
    },
    \Psr\EventDispatcher\EventDispatcherInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return new \Yiisoft\Yii\Debug\Collector\EventCollector(
            $container->get(Yiisoft\EventDispatcher\Dispatcher::class)
        );
    },
    \Psr\EventDispatcher\ListenerProviderInterface::class => function (\Psr\Container\ContainerInterface $container) {
        return new \Yiisoft\Yii\Debug\Collector\RequestCollector(
            $container->get(\Yiisoft\EventDispatcher\Provider\Provider::class)
        );
    },
];
