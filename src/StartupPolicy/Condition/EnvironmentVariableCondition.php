<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\StartupPolicy\Condition;

final class EnvironmentVariableCondition implements ConditionInterface
{
    public function __construct(
        private readonly string $variableName,
    ) {
    }

    public function match(object $event): bool
    {
        return (bool) getenv($this->variableName);
    }
}
