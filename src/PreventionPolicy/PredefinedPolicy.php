<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\PreventionPolicy;

final class PredefinedPolicy implements PreventionPolicyInterface
{
    public function __construct(
        private readonly bool $shouldPrevent,
    ) {
    }

    public function shouldPrevent(object $event): bool
    {
        return $this->shouldPrevent;
    }
}
