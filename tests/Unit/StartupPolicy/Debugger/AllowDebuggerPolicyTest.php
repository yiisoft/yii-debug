<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Debugger;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\PredefinedCondition;
use Yiisoft\Yii\Debug\StartupPolicy\Debugger\AllowDebuggerPolicy;

final class AllowDebuggerPolicyTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'empty' => [false, []];
        yield 'true' => [true, [new PredefinedCondition(true)]];
        yield 'false' => [false, [new PredefinedCondition(false)]];
        yield 'false-false-true' => [
            true,
            [
                new PredefinedCondition(false),
                new PredefinedCondition(false),
                new PredefinedCondition(true),
            ],
        ];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, array $conditions): void
    {
        $event = new stdClass();
        $policy = new AllowDebuggerPolicy(...$conditions);

        $this->assertSame($expected, $policy->satisfies($event));
    }
}
