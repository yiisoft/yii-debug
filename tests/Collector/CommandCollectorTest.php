<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\StringInput;
use Yiisoft\Yii\Console\Output\ConsoleBufferedOutput;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CommandCollector;

final class CommandCollectorTest extends CollectorTestCase
{
    /**
     * @param CollectorInterface|CommandCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(
            new ConsoleCommandEvent(
                new Command('test'),
                new StringInput('test'),
                new ConsoleBufferedOutput()
            )
        );
        $collector->collect(
            new ConsoleErrorEvent(
                new StringInput('test1'),
                new ConsoleBufferedOutput(),
                new Exception()
            )
        );
        $collector->collect(
            new ConsoleTerminateEvent(
                new Command('test1'),
                new StringInput('test1'),
                new ConsoleBufferedOutput(),
                0
            )
        );
    }

    public function testCollectWithInactiveCollector(): void
    {
        $collector = $this->getCollector();
        $this->collectTestData($collector);

        $collected = $collector->getCollected();
        $this->assertEmpty($collected);
    }

    protected function getCollector(): CollectorInterface
    {
        return new CommandCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);
        $collected = $collector->getCollected();
        $this->assertCount(3, $collected);
        $this->assertEquals('test', $collected[ConsoleCommandEvent::class]['input']);
        $this->assertEmpty($collected[ConsoleCommandEvent::class]['output']);
    }

    protected function checkIndexData(CollectorInterface $collector): void
    {
        parent::checkIndexData($collector);
        if ($collector instanceof CommandCollector) {
            $this->assertArrayHasKey('command', $collector->getIndexData());
            $this->assertArrayHasKey('input', $collector->getIndexData()['command']);
            $this->assertArrayHasKey('class', $collector->getIndexData()['command']);
            $this->assertEquals('test1', $collector->getIndexData()['command']['input']);
            $this->assertEquals(null, $collector->getIndexData()['command']['class']);
        }
    }
}
