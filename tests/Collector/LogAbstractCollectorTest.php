<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Log\LogLevel;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\LogCollector;

final class LogAbstractCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|LogCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(LogLevel::ALERT, 'test', ['context'], __FILE__ . ':' . __LINE__);
    }

    protected function getCollector(): CollectorInterface
    {
        return new LogCollector();
    }
}
