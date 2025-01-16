<?php

declare(strict_types=1);

use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\ReferencesArray;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\CommandCondition;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\OrCondition;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\EnvironmentVariableCondition;

if (!(bool)($params['yiisoft/yii-debug']['enabled'] ?? false)) {
    return [];
}

return [
    Debugger::class => [
        '__construct()' => [
            'collectors' => ReferencesArray::from(
                array_merge(
                    $params['yiisoft/yii-debug']['collectors'],
                    $params['yiisoft/yii-debug']['collectors.console'] ?? []
                )
            ),
            'startupPreventionCondition' => DynamicReference::to(
                static fn () => new OrCondition(
                    new EnvironmentVariableCondition('YII_DEBUG_IGNORE'),
                    new CommandCondition($params['yiisoft/yii-debug']['ignoredCommands'])
                ),
            ),
            'excludedClasses' => $params['yiisoft/yii-debug']['excludedClasses'],
        ],
    ],
];
