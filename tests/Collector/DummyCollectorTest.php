<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\DummyCollector;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class DummyCollectorTest extends AbstractCollectorTestCase
{
    protected function getCollector(TargetInterface $target): CollectorInterface
    {
        return new DummyCollector($target);
    }
}
