<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Storage;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;
use Yiisoft\Yii\Debug\DataNormalizer;
use Yiisoft\Yii\Debug\Storage\MemoryStorage;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

abstract class AbstractStorageTestCase extends TestCase
{
    public static function dataWriteAndRead(): iterable
    {
        yield 'integers1' => [
            ['collectorA' => [1, 2, 3]],
            [],
            ['count' => 3],
        ];
        yield 'string' => [
            ['collectorA' => ['string']],
            [],
            [],
        ];
        yield 'empty values' => [
            ['collectorA' => [['', 0, false]]],
            [],
            [],
        ];
        yield 'false' => [
            ['collectorA' => [false]],
            [],
            [],
        ];
        yield 'null' => [
            ['collectorA' => [null]],
            [],
            [],
        ];
        yield 'zero' => [
            ['collectorA' => [0]],
            [],
            [],
        ];
    }

    #[DataProvider('dataWriteAndRead')]
    public function testWriteAndRead(array $data, array $objectsMap, array $summary): void
    {
        $storage = $this->getStorage();

        $storage->write('test', $data, $objectsMap, $summary);

        $this->assertSame(['test' => $data], $storage->read(StorageInterface::TYPE_DATA, 'test'));
        $this->assertSame(['test' => $objectsMap], $storage->read(StorageInterface::TYPE_OBJECTS, 'test'));
        $this->assertSame(['test' => $summary], $storage->read(StorageInterface::TYPE_SUMMARY, 'test'));
    }

    abstract public function getStorage(): StorageInterface;

    public static function dataProvider(): iterable
    {
        yield 'integers' => [[1, 2, 3]];
        yield 'string' => [['string']];
        yield 'empty values' => [[[['', 0, false]]]];
        yield 'false' => [[false]];
        yield 'null' => [[null]];
        yield 'zero' => [[0]];
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
