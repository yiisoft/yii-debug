<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\PreventionPolicy;

final class CompositePolicy implements PreventionPolicyInterface
{
    /**
     * @psalm-var list<PreventionPolicyInterface>
     */
    private readonly array $policies;

    /**
     * @no-named-arguments
     */
    public function __construct(PreventionPolicyInterface ...$policies)
    {
        $this->policies = $policies;
    }

    public function shouldPrevent(object $event): bool
    {
        foreach ($this->policies as $policy) {
            if ($policy->shouldPrevent($event)) {
                return true;
            }
        }

        return false;
    }
}
