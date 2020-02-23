<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class LogCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\LogCollector $collector
     */
    protected function somethingDoTestExport(CollectorInterface $collector): void
    {
        $collector->dispatch(LogLevel::ALERT, 'test', ['context']);
    }

    protected function getCollector(): CollectorInterface
    {
        // Container should return Logger that implements CollectorInterface.
        $logCollector = $this->container->get(LoggerInterface::class);
        $this->assertInstanceOf(CollectorInterface::class, $logCollector);

        return $logCollector;
    }
}
