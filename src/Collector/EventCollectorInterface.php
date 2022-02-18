<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Event data collector responsibility is to collect verbose debug data during application lifecycle.
 */
interface EventCollectorInterface extends CollectorInterface
{
    /**
     * Collect data payload
     *
     * @param object $event
     * @param string $line
     */
    public function collect(object $event, string $line): void;
}
