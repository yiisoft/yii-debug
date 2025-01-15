<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class DebuggerTest extends TestCase
{
    public function testStartup(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('startup');
        $storage = new MemoryStorage();

        $debugger = new Debugger($storage, [$collector]);
        $debugger->startup(new stdClass());
    }

    public function testStartupWithSkipCollect(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('startup');
        $storage = new MemoryStorage();

        $debugger = new Debugger($storage, [$collector], ['/test']);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/debug')));
    }

    public function testWithIgnoredRequests(): void
    {
        $debugger1 = new Debugger(new MemoryStorage(), []);
        $debugger2 = $debugger1->withIgnoredRequests(['/test']);

        $this->assertNotSame($debugger1, $debugger2);
    }

    public function testIgnoreByHeader(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->never())->method('write');

        $debugger = new Debugger($storage, [$collector], []);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/test', ['X-Debug-Ignore' => 'true'])));
        $debugger->shutdown();
    }

    public function testWithIgnoredCommands(): void
    {
        $debugger1 = new Debugger(new MemoryStorage(), []);
        $debugger2 = $debugger1->withIgnoredCommands(['command/test']);

        $this->assertNotSame($debugger1, $debugger2);
    }

    public function testIgnoreByEnv(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->never())->method('write');

        putenv('YII_DEBUG_IGNORE=true');
        $debugger = new Debugger($storage, [$collector], []);
        $debugger->startup(new ApplicationStartup('command'));
        putenv('YII_DEBUG_IGNORE=false');
        $debugger->shutdown();
    }

    public function testShutdown(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->once())->method('write');

        $debugger = new Debugger($storage, [$collector]);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->shutdown();
        $debugger->shutdown();
        $debugger->shutdown();
    }

    public function testShutdownWithSkipRequestCollect(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->never())->method('write');

        $debugger = new Debugger($storage, [$collector], ['/test']);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->shutdown();
    }

    #[DataProvider('dataShutdownWithSkipCommandCollect')]
    public function testShutdownWithSkipCommandCollect(array $ignoredCommands, ?string $ignoredCommand): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->never())->method('startup');
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->never())->method('write');

        $debugger = new Debugger($storage, [$collector], [], $ignoredCommands);
        $debugger->startup(new ApplicationStartup($ignoredCommand));
        $debugger->shutdown();
    }

    public static function dataShutdownWithSkipCommandCollect(): iterable
    {
        yield [
            ['app:ignored-command'],
            'app:ignored-command',
        ];
        yield [
            ['app:ignored-command1', 'app:ignored-command2'],
            'app:ignored-command2',
        ];
        yield [
            ['app:ignored-command'],
            null,
        ];
        yield [
            ['app:ignored-command'],
            '',
        ];
    }

    #[DataProvider('dataShutdownWithoutSkipCommandCollect')]
    public function testShutdownWithoutSkipCommandCollect(array $ignoredCommands, ?string $ignoredCommand): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('startup');
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->once())->method('write');

        $debugger = new Debugger($storage, [$collector], [], $ignoredCommands);
        $debugger->startup(new ApplicationStartup($ignoredCommand));
        $debugger->shutdown();
    }

    public static function dataShutdownWithoutSkipCommandCollect(): iterable
    {
        yield [
            [],
            'app:not-ignored-command',
        ];
        yield [
            ['app:ignored-command'],
            'app:not-ignored-command',
        ];
    }

    public function testStopSkipped(): void
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())->method('shutdown');
        $storage = $this->getMockBuilder(StorageInterface::class)->getMock();
        $storage->expects($this->never())->method('clear');
        $storage->expects($this->never())->method('write');

        $debugger = new Debugger($storage, [$collector]);
        $debugger->startup(new BeforeRequest(new ServerRequest('GET', '/test')));
        $debugger->stop();
        $debugger->stop();
    }
}
