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
        $collector->collect(new BeforeRequest($this->createMock(ServerRequestInterface::class)));
        usleep(123_000);
        $collector->collect(new AfterRequest($this->createMock(ResponseInterface::class)));
    }

    protected function getCollector(): CollectorInterface
    {
        return new RequestCollector();
    }

    protected function assertExportedData(CollectorInterface $collector): void
    {
        parent::assertExportedData($collector);
        $data = $collector->collected();

        $this->assertGreaterThan(0.123, $data['request_processing_time']);
    }
}
