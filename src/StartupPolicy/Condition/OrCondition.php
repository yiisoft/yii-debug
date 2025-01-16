<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\StartupPolicy\Condition;

final class OrCondition implements ConditionInterface
{
    /**
     * @psalm-var list<ConditionInterface>
     */
    private readonly array $conditions;

    /**
     * @no-named-arguments
     */
    public function __construct(ConditionInterface ...$policies)
    {
        $this->conditions = $policies;
    }

    public function match(object $event): bool
    {
        foreach ($this->conditions as $policy) {
            if ($policy->match($event)) {
                return true;
            }
        }

        return false;
    }
}
