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
        $this->assertEquals([$data], $storage->getData());
    }

    /**
     * @dataProvider dataProvider()
     * @param array $data
     */
    public function testRead(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $collector = $this->createFakeCollector($data);

        $storage->addCollector($collector);
        $storage->flush();
        $this->assertEquals([$idGenerator->getId() => $storage->getData()], $storage->read());
    }

    /**
     * @dataProvider dataProvider()
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

    abstract public function getStorage(DebuggerIdGenerator $idGenerator): StorageInterface;

    public function dataProvider(): array
    {
        return [
            [[1, 2, 3]],
            [['string']],
            [[[['', 0, false]]]],
            [[]],
            [[false]],
            [[null]],
            [[0]],
            [[new \stdClass()]],
        ];
    }

    private function createFakeCollector(array $data)
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector
            ->method('getCollected')
            ->willReturn($data);

        return $collector;
    }
}
