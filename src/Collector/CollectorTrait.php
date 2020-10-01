<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

trait CollectorTrait
{
    private bool $isActive = false;

    public function startup(): void
    {
        $this->isActive = true;
    }

    public function shutdown(): void
    {
        $this->reset();
        $this->isActive = false;
    }

    private function isActive(): bool
    {
        return $this->isActive;
    }

    private function reset(): void
    {
    }
}
