<?php

return $params['debug.enabled'] ? [
    'bootstrap' => ['debug' => 'debug'],
    'app' => [
        'modules' => [
            'debug' => array_filter([
                '__class' => \yii\debug\Module::class,
                'allowedIPs' => $params['debug.allowedIPs'],
            ]),
        ],
    ],
] : [];
