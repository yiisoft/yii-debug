<?php

use Yiisoft\Yii\Debug\DebugServiceProvider;
use Yiisoft\Yii\Debug\ProxyServiceProvider;

return [
    'DebugContainerProxy' => ProxyServiceProvider::class,
    'Debugger' => DebugServiceProvider::class,
];
