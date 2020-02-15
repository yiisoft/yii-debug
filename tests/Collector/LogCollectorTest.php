<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Log\LoggerInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class LogCollectorTest extends AbstractCollectorTestCase
{
    protected function somethingDoTestExport(): void
    {
        $logger = $this->container->get(LoggerInterface::class);
        $logger->alert('test', ['context']);
    }

    protected function getCollector(TargetInterface $target): CollectorInterface
    {
        // Container should return Logger that implements CollectorInterface.
        $logCollector = $this->container->get(LoggerInterface::class);
        $this->assertInstanceOf(CollectorInterface::class, $logCollector);

        /* @var \Yiisoft\Yii\Debug\Collector\LogCollector $logCollector */
        $logCollector->setTarget($target);

        return $logCollector;
    }
}
