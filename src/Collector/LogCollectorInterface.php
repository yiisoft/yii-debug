<?php

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Logger data collector responsibility is to collect data during application lifecycle.
 */
interface LogCollectorInterface extends CollectorInterface
{
    /**
     * Collect data payload
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function collect(string $level, string $message, array $context): void;
}
