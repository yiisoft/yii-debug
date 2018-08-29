<?php

return $params['debug.enabled'] ? [
    'app' => [
        'bootstrap' => ['debug' => 'debug'],
        'modules' => [
            'debug' => array_filter([
                '__class' => \yii\debug\Module::class,
                'allowedIPs' => $params['debug.allowedIPs'],
                'panels' => [
                    'config' => [
                        '__class' => \yii\debug\panels\ConfigPanel::class,
                    ],
                    'request' => [
                        '__class' => \yii\debug\panels\RequestPanel::class,
                    ],
                    'log' => [
                        '__class' => \yii\debug\panels\LogPanel::class,
                    ],
                    'profiling' => [
                        '__class' => \yii\debug\panels\ProfilingPanel::class,
                    ],
                    'db' => [
                        '__class' => \yii\debug\panels\DbPanel::class,
                    ],
                    'event' => [
                        '__class' => \yii\debug\panels\EventPanel::class,
                    ],
                    'assets' => [
                        '__class' => \yii\debug\panels\AssetPanel::class,
                    ],
                    'mail' => [
                        '__class' => \yii\debug\panels\MailPanel::class,
                    ],
                    'timeline' => [
                        '__class' => \yii\debug\panels\TimelinePanel::class,
                    ],
                    'user' => [
                        '__class' => \yii\debug\panels\UserPanel::class,
                    ],
                    'router' => [
                        '__class' => \yii\debug\panels\RouterPanel::class,
                    ],
                ],
            ]),
        ],
    ],
] : [];
