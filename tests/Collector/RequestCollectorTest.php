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
        sleep(1);
        $dispatcher->dispatch(new RequestEndEvent());
    }

    protected function getCollector(TargetInterface $target): CollectorInterface
    {
        // Container should return EventDispatcher that implements CollectorInterface.
        $dispatcher = $this->container->get(ListenerProviderInterface::class);
        $this->assertInstanceOf(CollectorInterface::class, $dispatcher);

        /* @var \Yiisoft\Yii\Debug\Collector\CollectorInterface $dispatcher */
        $dispatcher->setTarget($target);

        return $dispatcher;
    }
}
