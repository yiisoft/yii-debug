<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\PreventionPolicy;

interface PreventionPolicyInterface
{
    public function shouldPrevent(object $event): bool;
}
