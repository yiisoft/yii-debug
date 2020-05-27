<?php

use Yiisoft\Yii\Debug\ProxyServiceProvider;

if (!(bool)($params['yiisoft/yii-debugger']['enabled'] ?? false)) {
    return [];
}

return [
    'yiisoft/yii-debugger/Debugger' => ProxyServiceProvider::class
];
