<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Router data collector responsibility is to collect data during application lifecycle.
 */
interface RouterCollectorInterface extends CollectorInterface
{
    /**
     * Collect data payload
     *
     * @param float $matchTime
     */
    public function collect(float $matchTime): void;
}
