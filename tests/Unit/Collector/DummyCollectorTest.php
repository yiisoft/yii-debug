<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use stdClass;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Tests\Shared\AbstractCollectorTestCase;

final class DummyCollectorTest extends AbstractCollectorTestCase
{
    protected function getCollector(): CollectorInterface
    {
        $collector = $this->createMock(CollectorInterface::class);
        $collector->method('getCollected')
            ->willReturn(
                [
                    'int' => 123,
                    'str' => 'asdas',
                    'object' => new stdClass(),
                ]
            );
        $collector->method('getName')
            ->willReturn($collector::class);

        return $collector;
    }

    protected function collectTestData(CollectorInterface $collector): void
    {
        // pass
    }
}
