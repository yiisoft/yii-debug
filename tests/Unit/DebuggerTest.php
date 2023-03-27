<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class DebuggerTest extends TestCase
{
    public function testStartup(): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('startup');

        $debugger = new Debugger($idGenerator, new MemoryStorage($idGenerator), [$collector]);
        $debugger->startup(new stdClass());
    }

    public function testStartupWithSkipCollect(): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('startup');

        $debugger = new Debugger($idGenerator, new MemoryStorage($idGenerator), [$collector], ['/test']);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/debug')));
    }

    public function testGetId(): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $debugger = new Debugger($idGenerator, new MemoryStorage($idGenerator), []);

        $this->assertEquals($idGenerator->getId(), $debugger->getId());
    }

    public function testWithIgnoredRequests(): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $debugger1 = new Debugger($idGenerator, new MemoryStorage($idGenerator), []);
        $debugger2 = $debugger1->withIgnoredRequests(['/test']);

        $this->assertNotSame($debugger1, $debugger2);
    }

    public function testWithIgnoredCommands(): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $debugger1 = new Debugger($idGenerator, new MemoryStorage($idGenerator), []);
        $debugger2 = $debugger1->withIgnoredCommands(['command/test']);

        $this->assertNotSame($debugger1, $debugger2);
    }

    public function testShutdown(): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');

        $debugger = new Debugger($idGenerator, new MemoryStorage($idGenerator), [$collector]);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->shutdown();
    }

    public function testShutdownWithSkipCollect(): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->exactly(0))->method('flush');

        $debugger = new Debugger($idGenerator, $storage, [$collector], ['/test']);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->shutdown();
    }
}
