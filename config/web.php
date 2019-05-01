<?php

return $params['debug.enabled'] ? [
    'app' => [
        'bootstrap' => ['debug' => 'debug'],
        'modules' => [
            'debug' => array_filter([
                '__class' => \Yiisoft\Yii\Debug\Module::class,
                'allowedIPs' => $params['debug.allowedIPs'],
                'panels' => [
                    'config' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\ConfigPanel::class,
                    ],
                    'request' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\RequestPanel::class,
                    ],
                    'log' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\LogPanel::class,
                    ],
                    'profiling' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\ProfilingPanel::class,
                    ],
                    'db' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\DbPanel::class,
                    ],
                    'event' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\EventPanel::class,
                    ],
                    'assets' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\AssetPanel::class,
                    ],
                    'mail' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\MailPanel::class,
                    ],
                    'timeline' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\TimelinePanel::class,
                    ],
                    'user' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\UserPanel::class,
                    ],
                    'router' => [
                        '__class' => \Yiisoft\Yii\Debug\Panels\RouterPanel::class,
                    ],
                ],
            ]),
        ],
    ],
] : [];
