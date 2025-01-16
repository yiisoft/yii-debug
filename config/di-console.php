<?php

declare(strict_types=1);

use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\ReferencesArray;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\PreventionPolicy\CommandPolicy;
use Yiisoft\Yii\Debug\PreventionPolicy\CompositePolicy;
use Yiisoft\Yii\Debug\PreventionPolicy\EnvironmentVariablePolicy;

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
            'startupPreventionPolicy' => DynamicReference::to(
                static fn () => new CompositePolicy(
                    new EnvironmentVariablePolicy(),
                    new CommandPolicy($params['yiisoft/yii-debug']['ignoredCommands'])
                ),
            ),
            'excludedClasses' => $params['yiisoft/yii-debug']['excludedClasses'],
        ],
    ],
];
