<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\PredefinedCondition;

final class PredefinedConditionTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'true' => [true];
        yield 'false' => [false];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $value): void
    {
        $event = new stdClass();
        $condition = new PredefinedCondition($value);

        $this->assertSame($value, $condition->match($event));
    }
}
