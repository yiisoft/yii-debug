<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Storage;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

abstract class AbstractStorageTest extends TestCase
{
    /**
     * @dataProvider dataProvider()
     *
     * @param array $data
     */
    public function testAddAndGet(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())
            ->method('getCollected')
            ->willReturn($data);

        $this->assertEquals([], $storage->getData());
        $storage->addCollector($collector);
        $this->assertEquals([get_class($collector) => $data], $storage->getData());
    }

    /**
     * @dataProvider dataProvider()
     *
     * @param array $data
     */
    public function testRead(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $collector = $this->createFakeCollector($data);

        $storage->addCollector($collector);
        $storage->flush();
        $this->assertIsArray($storage->read());
        $this->assertEquals($storage->getData(), $storage->read(StorageInterface::TYPE_DATA));
    }

    /**
     * @dataProvider dataProvider()
     *
     * @param array $data
     */
    public function testFlush(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $collector = $this->createFakeCollector($data);

        $storage->addCollector($collector);
        $storage->flush();
        $this->assertEquals([], $storage->getData());
    }

    /**
     * @dataProvider dataProvider()
     *
     * @param array $data
     */
    public function testClear(array $data): void
    {
        $this->expectNotToPerformAssertions();
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $collector = $this->createFakeCollector($data);

        $storage->addCollector($collector);
        $storage->flush();
        $storage->clear();
    }

    abstract public function getStorage(DebuggerIdGenerator $idGenerator): StorageInterface;

    public function dataProvider(): array
    {
        return [
            [[1, 2, 3]],
            [['string']],
            [[[['', 0, false]]]],
            [['test']],
            [[false]],
            [[null]],
            [[0]],
            [[new \stdClass()]],
        ];
    }

    protected function createFakeCollector(array $data)
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector
            ->method('getCollected')
            ->willReturn($data);

        return $collector;
    }
}
