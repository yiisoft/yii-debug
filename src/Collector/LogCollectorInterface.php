<?php

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Logger data collector responsibility is to collect data during application lifecycle.
 */
interface LogCollectorInterface extends CollectorInterface
{
    /**
     * Collect data payload
     * @param mixed ...$payload
     */
    public function collect(...$payload): void;
}
