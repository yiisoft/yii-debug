<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use stdClass;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\EventDispatcherInterfaceProxy;

final class EventDispatcherProxyTest extends TestCase
{
    public function testDispatch(): void
    {
        $event = new stdClass();
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $collector = $this->createMock(EventCollector::class);

        $eventDispatcher->method('dispatch')->willReturn($event);
        $collector
            ->expects($this->once())
            ->method('collect')
            ->with($event, __FILE__ . ':29');

        $proxy = new EventDispatcherInterfaceProxy($eventDispatcher, $collector);

        $proxy->dispatch($event);
    }
}
