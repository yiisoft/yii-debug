<?php

namespace Yiisoft\Yii\Debug\Collector;

interface CollectorInterface
{
    public function startup(): void;

    public function shutdown(): void;

    public function collect(): array;
}
