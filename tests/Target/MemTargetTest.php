<?php

namespace Yiisoft\Yii\Debug\Tests\Target;

use Yiisoft\Yii\Debug\Target\MemTarget;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class MemTargetTest extends AbstractTargetTest
{
    public function getTarget(): TargetInterface
    {
        return new MemTarget();
    }
}
