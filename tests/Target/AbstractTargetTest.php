<?php

namespace Yiisoft\Yii\Debug\Tests\Target;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Target\TargetInterface;

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
        $target->persist($collector);
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

        $target->persist($collector);
        $target->flush();
        $this->assertEquals([], $target->getData());
    }

    abstract public function getTarget(): TargetInterface;

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
