<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Console\Event\ApplicationShutdown;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\ConsoleAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\WebAppInfoCollector;

final class ConsoleAppInfoCollectorTest extends CollectorTestCase
{
    /**
     * @param CollectorInterface|WebAppInfoCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new ApplicationStartup(null));
        usleep(123_000);
        $collector->collect(new ApplicationShutdown(0));
    }

    protected function getCollector(): CollectorInterface
    {
        return new ConsoleAppInfoCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->markTestSkipped('This test is not supported on Windows.');
        }

        parent::checkCollectedData($collector);
        $data = $collector->getCollected();

        $this->assertGreaterThan(0.122, $data['applicationProcessingTime']);
    }
}
