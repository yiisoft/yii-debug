<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use Yiisoft\Yii\Console\Event\ApplicationShutdown;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector;
use Yiisoft\Yii\Debug\Collector\Web\WebAppInfoCollector;

use Yiisoft\Yii\Debug\Tests\Shared\AbstractCollectorTestCase;

use function sleep;
use function usleep;

final class ConsoleAppInfoCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|WebAppInfoCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new ApplicationStartup(null));

        DIRECTORY_SEPARATOR === '\\' ? sleep(1) : usleep(123_000);

        $collector->collect(new ApplicationShutdown(0));
    }

    protected function getCollector(): CollectorInterface
    {
        return new ConsoleAppInfoCollector();
    }

    protected function checkCollectedData(array $data): void
    {
        parent::checkCollectedData($data);

        $this->assertGreaterThan(0.122, $data['applicationProcessingTime']);
    }
}
