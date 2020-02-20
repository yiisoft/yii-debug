<?php

namespace Yiisoft\Yii\Debug\Tests\Target;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Target\TargetInterface;

abstract class AbstractTargetTestCase extends TestCase
{
    /**
     * @dataProvider dataProvider()
     * @param array $data
     */
    public function testAddAndGet(...$data): void
    {
        $target = $this->getTarget();

        $this->assertEquals([], $target->getData());
        $target->persist(...$data);
        $this->assertEquals($data, $target->getData());
    }

    /**
     * @dataProvider dataProvider()
     * @param array $data
     */
    public function testFlush(...$data): void
    {
        $target = $this->getTarget();

        $target->persist(...$data);
        $this->assertEquals($data, $target->getData());
        $target->flush();
        $this->assertEquals([], $target->getData());
    }

    abstract public function getTarget(): TargetInterface;

    public function dataProvider(): array
    {
        return [
            [1, 2, 3],
            ['string'],
            [[['', 0, false]]],
            [],
            [false],
            [null],
            [0],
            [new \stdClass()],
        ];
    }
}
