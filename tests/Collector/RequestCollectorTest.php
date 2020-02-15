<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Event\RequestEndEvent;
use Yiisoft\Yii\Debug\Event\RequestStartedEvent;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class RequestCollectorTest extends AbstractCollectorTestCase
{
    protected function somethingDoTestExport(): void
    {
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->dispatch(new RequestStartedEvent());
        usleep(123_000);
        $dispatcher->dispatch(new RequestEndEvent());
    }

    protected function getCollector(TargetInterface $target): CollectorInterface
    {
        // Container should return EventDispatcher that implements CollectorInterface.
        $provider = $this->container->get(ListenerProviderInterface::class);
        $this->assertInstanceOf(CollectorInterface::class, $provider);

        /* @var \Yiisoft\Yii\Debug\Collector\CollectorInterface $provider */
        $provider->setTarget($target);

        return $provider;
    }

    protected function assertExportedData(TargetInterface $target): void
    {
        parent::assertExportedData($target);
        $data = $target->getData();
        $this->assertGreaterThan(0.123, $data[2]);
    }
}
