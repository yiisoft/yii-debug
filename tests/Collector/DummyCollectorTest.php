<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class DummyCollectorTest extends AbstractCollectorTestCase
{
    protected function getCollector(): CollectorInterface
    {
        return new DummyCollector();
    }
}

final class DummyCollector implements CollectorInterface
{
    public function collect(): array
    {
        return [
            'int' => 123,
            'str' => 'asdas',
            'object' => new \stdClass(),
        ];
    }

    public function startup(): void
    {
    }

    public function shutdown(): void
    {
    }
}
