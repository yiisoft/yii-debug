<?php

return $params['debug.enabled'] ? [
    'app' => [
        'bootstrap' => ['debug' => 'debug'],
        'modules' => [
            'debug' => array_filter([
                '__class' => \Yiisoft\Debug\Module::class,
                'allowedIPs' => $params['debug.allowedIPs'],
                'panels' => [
                    'config' => [
                        '__class' => \Yiisoft\Debug\Panels\ConfigPanel::class,
                    ],
                    'request' => [
                        '__class' => \Yiisoft\Debug\Panels\RequestPanel::class,
                    ],
                    'log' => [
                        '__class' => \Yiisoft\Debug\Panels\LogPanel::class,
                    ],
                    'profiling' => [
                        '__class' => \Yiisoft\Debug\Panels\ProfilingPanel::class,
                    ],
                    'db' => [
                        '__class' => \Yiisoft\Debug\Panels\DbPanel::class,
                    ],
                    'event' => [
                        '__class' => \Yiisoft\Debug\Panels\EventPanel::class,
                    ],
                    'assets' => [
                        '__class' => \Yiisoft\Debug\Panels\AssetPanel::class,
                    ],
                    'mail' => [
                        '__class' => \Yiisoft\Debug\Panels\MailPanel::class,
                    ],
                    'timeline' => [
                        '__class' => \Yiisoft\Debug\Panels\TimelinePanel::class,
                    ],
                    'user' => [
                        '__class' => \Yiisoft\Debug\Panels\UserPanel::class,
                    ],
                    'router' => [
                        '__class' => \Yiisoft\Debug\Panels\RouterPanel::class,
                    ],
                ],
            ]),
        ],
    ],
] : [];
