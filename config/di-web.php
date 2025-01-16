<?php

declare(strict_types=1);

use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\ReferencesArray;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\PreventionPolicy\CompositePolicy;
use Yiisoft\Yii\Debug\PreventionPolicy\EnvironmentVariablePolicy;
use Yiisoft\Yii\Debug\PreventionPolicy\HeaderPolicy;
use Yiisoft\Yii\Debug\PreventionPolicy\RequestPolicy;

if (!(bool)($params['yiisoft/yii-debug']['enabled'] ?? false)) {
    return [];
}

return [
    Debugger::class => [
        '__construct()' => [
            'collectors' => ReferencesArray::from(
                array_merge(
                    $params['yiisoft/yii-debug']['collectors'],
                    $params['yiisoft/yii-debug']['collectors.web'] ?? [],
                )
            ),
            'startupPreventionPolicy' => DynamicReference::to(
                static fn () => new CompositePolicy(
                    new EnvironmentVariablePolicy(),
                    new HeaderPolicy(),
                    new RequestPolicy($params['yiisoft/yii-debug']['ignoredRequests'])
                ),
            ),
            'excludedClasses' => $params['yiisoft/yii-debug']['excludedClasses'],
        ],
    ],
];
