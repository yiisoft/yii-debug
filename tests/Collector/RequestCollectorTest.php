<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;

class RequestCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|RequestCollector $collector
     */
    protected function somethingDoTestExport(CollectorInterface $collector): void
    {
        $collector->dispatch(new BeforeRequest($this->createMock(ServerRequestInterface::class)));
        usleep(123_000);
        $collector->dispatch(new AfterRequest($this->createMock(ResponseInterface::class)));
    }

    protected function getCollector(): CollectorInterface
    {
        // Container should return EventDispatcher that implements CollectorInterface.
        $collector = $this->container->get(RequestCollector::class);
        $this->assertInstanceOf(CollectorInterface::class, $collector);

        return $collector;
    }

    protected function assertExportedData(CollectorInterface $collector): void
    {
        parent::assertExportedData($collector);
        $data = $collector->collect();

        $this->assertGreaterThan(0.123, $data['request_processing_time']);
    }
}
