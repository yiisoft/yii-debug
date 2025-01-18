<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Collector;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Collector\CallableCollectorPolicy;
use Yiisoft\Yii\Debug\Tests\Support\StubCollector;

final class CallableCollectorPolicyTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'true' => [true, static fn ($collector, $event) => true];
        yield 'false' => [false, static fn ($collector, $event) => false];
        yield 'check-arguments' => [true, static fn (StubCollector $collector, stdClass $event) => true];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, callable $callable): void
    {
        $event = new stdClass();
        $collector = new StubCollector();

        $policy = new CallableCollectorPolicy($callable);

        $this->assertSame($expected, $policy->satisfies($collector, $event));
    }
}
