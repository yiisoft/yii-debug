<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Storage;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;
use Yiisoft\Yii\Debug\DataNormalizer;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

use function json_decode;

abstract class AbstractStorageTestCase extends TestCase
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

    #[DataProvider('dataProvider')]
    public function testRead(array $data): void
    {
        $dataNormalizer = new DataNormalizer();

        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->getStorage($idGenerator);

        $storage->addCollector($this->createFakeCollector($data));
        $storage->addCollector($this->createFakeSummaryCollector($data));
        $expectedData = $storage->getData();
        [$normalizedExpectedData, ] = $dataNormalizer->prepareDataAndObjectsMap($expectedData);
        if (!$storage instanceof MemoryStorage) {
            $storage->flush();
        }

        $result = $storage->read(StorageInterface::TYPE_DATA);
        [$normalizedResult, ] = $dataNormalizer->prepareDataAndObjectsMap($result);
        $this->assertEquals([$idGenerator->getId() => $normalizedExpectedData], $normalizedResult);
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
//        yield 'integers' => [[1, 2, 3]];
//        yield 'string' => [['string']];
//        yield 'empty values' => [[[['', 0, false]]]];
//        yield 'false' => [[false]];
//        yield 'null' => [[null]];
//        yield 'zero' => [[0]];
        yield 'stdClass' => [[new stdClass()]];
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
