<?php

namespace Yiisoft\Yii\Debug\Tests\Storage;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

abstract class AbstractTargetTest extends TestCase
{
    /**
     * @dataProvider dataProvider()
     * @param array $data
     */
    public function testAddAndGet(array $data): void
    {
        $target = $this->getTarget();
        $collector = $this->getMockBuilder(CollectorInterface::class)->getMock();
        $collector->expects($this->once())
            ->method('collect')
            ->willReturn($data);

        $this->assertEquals([], $target->getData());
        $target->addCollector($collector);
        $this->assertEquals([$data], $target->getData());
    }

    /**
     * @dataProvider dataProvider()
     * @param array $data
     */
    public function testFlush(array $data): void
    {
        $this->markTestIncomplete('the "getData" method should return an empty array after flush');
        $target = $this->getTarget();
        $collector = $this->createFakeCollector($data);

        $target->addCollector($collector);
        $target->flush();
        $this->assertEquals([], $target->getData());
    }

    abstract public function getTarget(): StorageInterface;

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
        $collector->expects($this->once())
            ->method('collect')
            ->willReturn($data);

        return $collector;
    }
}
