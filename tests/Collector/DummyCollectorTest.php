<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\DummyCollector;

class DummyCollectorTest extends AbstractCollectorTestCase
{
    protected function getCollector(): CollectorInterface
    {
        return new DummyCollector();
    }
}
