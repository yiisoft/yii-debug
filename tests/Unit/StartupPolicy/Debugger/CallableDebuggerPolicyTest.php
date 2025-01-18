<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Debugger;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Debugger\CallableDebuggerPolicy;

final class CallableDebuggerPolicyTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'true' => [true, static fn ($event) => true];
        yield 'false' => [false, static fn ($event) => false];
        yield 'check-arguments' => [true, static fn (stdClass $event) => true];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, callable $callable): void
    {
        $event = new stdClass();
        $policy = new CallableDebuggerPolicy($callable);

        $this->assertSame($expected, $policy->satisfies($event));
    }
}
