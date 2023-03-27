<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Index data collector responsibility is to collect index data during application lifecycle.
 * Index is used to display a list of previous requests and select one to display full info.
 * Its data set is specific to the list and is reduced compared to full data collected
 * in {@see CollectorInterface}.
 */
interface IndexCollectorInterface extends CollectorInterface
{
    /**
     * @return array data indexed
     */
    public function getIndexData(): array;
}
