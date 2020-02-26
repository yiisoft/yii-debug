<?php

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Debug data collector responsibility is to collect data during application lifecycle.
 */
interface CollectorInterface
{
    /**
     * Called once at application startup.
     * Any initialization could be done here.
     */
    public function startup(): void;

    /**
     * Called once at application shutdown.
     * Cleanup could be done here.
     */
    public function shutdown(): void;

    /**
     * @return array data collected
     */
    public function getCollected(): array;

    /**
     * Collect data payload
     * @param mixed ...$payload
     */
    public function collect(...$payload): void;
}
