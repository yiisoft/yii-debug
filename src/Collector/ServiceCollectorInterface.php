<?php

namespace Yiisoft\Yii\Debug\Collector;

interface ServiceCollectorInterface extends CollectorInterface
{
    public function collect(
        string $service,
        string $class,
        string $method,
        array $arguments,
        $result,
        string $status,
        ?object $error,
        float $timeStart,
        float $timeEnd
    ): void;
}
