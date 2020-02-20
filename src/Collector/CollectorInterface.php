<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Debug\Target\TargetInterface;

interface CollectorInterface
{
    public function startup(): void;

    public function shutdown(): void;

    public function export(): void;

    public function setTarget(TargetInterface $target): void;
}
