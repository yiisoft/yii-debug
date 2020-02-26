<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

final class DummyCollectorTest extends CollectorTestCase
{
    protected function getCollector(): CollectorInterface
    {
        $collector = $this->createMock(CollectorInterface::class);
        $collector->method('getCollected')
            ->willReturn(
                [
                    'int' => 123,
                    'str' => 'asdas',
                    'object' => new \stdClass(),
                ]
            );

        return $collector;
    }

    protected function collectTestData(CollectorInterface $collector): void
    {
        // pass
    }
}
