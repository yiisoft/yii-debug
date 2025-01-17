<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Debugger;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\PredefinedCondition;
use Yiisoft\Yii\Debug\StartupPolicy\Debugger\DenyDebuggerPolicy;

final class DenyDebuggerPolicyTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'empty' => [true, []];
        yield 'true' => [false, [new PredefinedCondition(true)]];
        yield 'false' => [true, [new PredefinedCondition(false)]];
        yield 'false-false-true' => [
            false,
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
        $policy = new DenyDebuggerPolicy(...$conditions);

        $this->assertSame($expected, $policy->satisfies($event));
    }
}
