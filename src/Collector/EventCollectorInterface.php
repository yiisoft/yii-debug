<?php

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Event data collector responsibility is to collect data during application lifecycle.
 */
interface EventCollectorInterface extends CollectorInterface
{
    /**
     * Collect data payload
     * @param object $event
     */
    public function collect(object $event): void;
}
