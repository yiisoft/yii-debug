<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use stdClass;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\EventDispatcherInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\TimelineCollector;

final class EventDispatcherInterfaceProxyTest extends TestCase
{
    public function testDispatch()
    {
        $event = new stdClass();
        $collector = new EventCollector(new TimelineCollector());
        $collector->startup();

        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with($event)
            ->willReturn($event);
        $eventDispatcher = new EventDispatcherInterfaceProxy($eventDispatcherMock, $collector);

        $newEvent = $eventDispatcher->dispatch($event);

        $this->assertSame($event, $newEvent);
        $this->assertCount(1, $collector->getCollected());
    }
}
