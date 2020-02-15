<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Debug\Target\TargetInterface;

interface CollectorInterface
{
    public function export(): void;

    public function setTarget(TargetInterface $target): void;
}
