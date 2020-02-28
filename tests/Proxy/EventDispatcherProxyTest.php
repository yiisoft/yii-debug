<?php

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\EventCollectorInterface;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Debug\Proxy\EventDispatcherProxy;
use Yiisoft\Yii\Debug\DebugEventDispatcher;
use Yiisoft\Yii\Debug\Debugger;

final class EventDispatcherProxyTest extends TestCase
{
    public function testDispatch(): void
    {
        $event = new \stdClass();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $debugEventDispatcher = $this->createMock(DebugEventDispatcher::class);
        $storage = $this->createMock(StorageInterface::class);
        $debugger = new Debugger($storage, []);
        $collector = $this->createMock(EventCollectorInterface::class);
        $collector
            ->expects($this->once())
            ->method('collect')
            ->with($event);

        $proxy = new EventDispatcherProxy($eventDispatcher, $debugEventDispatcher, $collector, $debugger);

        $proxy->dispatch($event);
    }
}
