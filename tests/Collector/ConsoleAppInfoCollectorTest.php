<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Yii\Console\Event\ApplicationShutdown;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\ConsoleAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\WebAppInfoCollector;

use function microtime;
use function time_sleep_until;

final class ConsoleAppInfoCollectorTest extends CollectorTestCase
{
    /**
     * @param CollectorInterface|WebAppInfoCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new ApplicationStartup(null));

        time_sleep_until(microtime(true) + 0.123);

        $collector->collect(new ApplicationShutdown(0));
    }

    protected function getCollector(): CollectorInterface
    {
        return new ConsoleAppInfoCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);

        $data = $collector->getCollected();

        $this->assertGreaterThan(0.122, $data['applicationProcessingTime']);
    }
}
