<?php

namespace Yiisoft\Yii\Debug\Collector;

/**
 * Index data collector responsibility is to collect data during application lifecycle.
 */
interface IndexCollectorInterface
{
    /**
     * @return array data indexed
     */
    public function getIndexed(): array;
}
