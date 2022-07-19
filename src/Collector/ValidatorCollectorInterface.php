<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Validator\Result;

/**
 * Logger data collector responsibility is to collect data during application lifecycle.
 */
interface ValidatorCollectorInterface extends CollectorInterface
{
    public function collect(mixed $value, iterable $rules, Result $result): void;
}
