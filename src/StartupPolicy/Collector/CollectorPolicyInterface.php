<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\StartupPolicy\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

interface CollectorPolicyInterface
{
    public function shouldStartup(CollectorInterface $collector, object $event): bool;
}
