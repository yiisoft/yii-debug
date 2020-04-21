<?php

use Yiisoft\Yii\Debug\ProxyServiceProvider;

if (!(bool)($params['debugger.enabled'] ?? false)) {
    return [];
}

return [
    'Debugger' => ProxyServiceProvider::class
];
