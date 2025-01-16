<?php

declare(strict_types=1);

use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\ReferencesArray;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\OrCondition;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\EnvironmentVariableCondition;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\HeaderCondition;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\RequestCondition;

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
            'startupPreventionCondition' => DynamicReference::to(
                static fn () => new OrCondition(
                    new EnvironmentVariableCondition('YII_DEBUG_IGNORE'),
                    new HeaderCondition('X-Debug-Ignore'),
                    new RequestCondition($params['yiisoft/yii-debug']['ignoredRequests'])
                ),
            ),
            'excludedClasses' => $params['yiisoft/yii-debug']['excludedClasses'],
        ],
    ],
];
