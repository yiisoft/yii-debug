<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Log\LoggerInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class LogCollectorTest extends AbstractCollectorTestCase
{
    protected function somethingDoTestExport(): void
    {
        $logger = $this->container->get(LoggerInterface::class);
        $logger->alert('test', ['context']);
    }

    protected function getCollector(): CollectorInterface
    {
        // Container should return Logger that implements CollectorInterface.
        $logCollector = $this->container->get(LoggerInterface::class);
        $this->assertInstanceOf(CollectorInterface::class, $logCollector);

        return $logCollector;
    }
}
