<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Event\RequestEndEvent;
use Yiisoft\Yii\Debug\Event\RequestStartedEvent;

class RequestCollectorTest extends AbstractCollectorTestCase
{
    protected function somethingDoTestExport(): void
    {
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->dispatch(new RequestStartedEvent());
        usleep(123_000);
        $dispatcher->dispatch(new RequestEndEvent());
    }

    protected function getCollector(): CollectorInterface
    {
        // Container should return EventDispatcher that implements CollectorInterface.
        $provider = $this->container->get(ListenerProviderInterface::class);
        $this->assertInstanceOf(CollectorInterface::class, $provider);

        return $provider;
    }

    protected function assertExportedData(CollectorInterface $collector): void
    {
        parent::assertExportedData($collector);
        $data = $collector->collect();

        $this->assertGreaterThan(0.123, $data['processing_time']);
    }
}
