<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Yiisoft\Yii\Console\Output\ConsoleBufferedOutput;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CommandCollector;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

final class CommandCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\CommandCollector $collector
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
    }

    protected function getCollector(): CollectorInterface
    {
        return new CommandCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);
        $collected = $collector->getCollected();
        $this->assertCount(1, $collected);
        $this->assertEquals('test', $collected[ConsoleCommandEvent::class]['input']);
        $this->assertEmpty($collected[ConsoleCommandEvent::class]['output']);
    }

    protected function checkIndexData(CollectorInterface $collector): void
    {
        parent::checkIndexData($collector);
        if ($collector instanceof IndexCollectorInterface) {
            $this->assertArrayHasKey('command', $collector->getIndexData());
            $this->assertEquals('test', $collector->getIndexData()['command']);
        }
    }
}
