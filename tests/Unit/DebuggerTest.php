<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\StartupPolicy\Debugger\AllowDebuggerPolicy;
use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class DebuggerTest extends TestCase
{
    public function testStart(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('startup');
        $storage = new MemoryStorage();

        $debugger = new Debugger($storage, [$collector]);
        $debugger->start(new stdClass());
    }

    public function testStop(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->once())->method('write');

        $debugger = new Debugger($storage, [$collector]);
        $debugger->start(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->stop();
        $debugger->stop();
        $debugger->stop();
    }

    public function testStopWithStartupPrevention(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->never())->method('startup');
        $collector->expects($this->never())->method('shutdown');

        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->never())->method('write');

        $debugger = new Debugger($storage, [$collector], new AllowDebuggerPolicy());
        $debugger->start(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->stop();
    }

    public function testStopSkipped(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->never())->method('clear');
        $storage->expects($this->never())->method('write');

        $debugger = new Debugger($storage, [$collector]);
        $debugger->start(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->kill();
        $debugger->kill();
    }
}
