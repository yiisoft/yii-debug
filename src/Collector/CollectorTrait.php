<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Debug\Target\TargetInterface;

trait CollectorTrait
{
    private ?TargetInterface $target = null;
    private bool $isActive = false;

    public function startup(): void
    {
        $this->isActive = true;
    }

    public function shutdown(): void
    {
        $this->isActive = false;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setTarget(TargetInterface $target): void
    {
        $this->target = $target;
    }
}
