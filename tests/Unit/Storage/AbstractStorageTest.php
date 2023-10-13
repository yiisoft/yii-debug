<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Storage;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Dumper;
use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

abstract class AbstractStorageTest extends TestCase
{
    /**
     * @dataProvider dataProvider()
     */
    public function testAddAndGet(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);
        $collector = $this->createFakeCollector($data);

        $this->assertEquals([], $storage->getData());
        $storage->addCollector($collector);
        $this->assertEquals([$collector->getName() => $data], $storage->getData());
    }

    /**
     * @dataProvider dataProvider()
     */
    public function testRead(array $data): void
    {
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);

        $storage->addCollector($this->createFakeCollector($data));
        $storage->addCollector($this->createFakeSummaryCollector($data));
        $expectedData = $storage->getData();
        $encodedExpectedData = \json_decode(Dumper::create($expectedData)->asJson(), true);

        if (!$storage instanceof MemoryStorage) {
            $storage->flush();
        }

        $result = $storage->read(StorageInterface::TYPE_DATA);
        $encodedResult = \json_decode(Dumper::create($result)->asJson(), true);
        $this->assertEquals([$idGenerator->getId() => $encodedExpectedData], $encodedResult);
    }

    /**
     * @dataProvider dataProvider()
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

    public static function dataProvider(): iterable
    {
        yield [[1, 2, 3]];
        yield [['string']];
        yield [[[['', 0, false]]]];
        yield [['test']];
        yield [[false]];
        yield [[null]];
        yield [[0]];
        yield [[new stdClass()]];
    }

    protected function createFakeCollector(array $data)
    {
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector
            ->method('getCollected')
            ->willReturn($data);
        $collector
            ->method('getName')
            ->willReturn('Mock_Collector');

        return $collector;
    }

    protected function createFakeSummaryCollector(array $data)
    {
        $collector = $this->getMockBuilder(SummaryCollectorInterface::class)->getMock();
        $collector
            ->method('getCollected')
            ->willReturn($data);
        $collector
            ->method('getName')
            ->willReturn('SummaryMock_Collector');

        $collector
            ->method('getSummary')
            ->willReturn(['summary' => 'summary data']);

        return $collector;
    }
}
