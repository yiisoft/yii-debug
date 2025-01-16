<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\PreventionPolicy;

final class EnvironmentVariablePolicy implements PreventionPolicyInterface
{
    public function __construct(
        private readonly string $variableName = 'YII_DEBUG_IGNORE',
    ) {
    }

    public function shouldPrevent(object $event): bool
    {
        return (bool) getenv($this->variableName);
    }
}
