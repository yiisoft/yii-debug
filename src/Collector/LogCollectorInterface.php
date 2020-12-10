<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Logger data collector responsibility is to collect data during application lifecycle.
 */
interface LogCollectorInterface extends CollectorInterface
{
    /**
     * Collect data payload
     *
     * @param string $level
     * @param mixed $message
     * @param array $context
     */
    public function collect(string $level, $message, array $context): void;
}
