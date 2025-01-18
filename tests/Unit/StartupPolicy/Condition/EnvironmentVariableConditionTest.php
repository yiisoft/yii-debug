<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\EnvironmentVariableCondition;

final class EnvironmentVariableConditionTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'true' => [true, 'true'];
        yield 'false' => [false, 'false'];
        yield 'on' => [true, 'on'];
        yield 'off' => [false, 'off'];
        yield 'TRUE' => [true, 'TRUE'];
        yield 'FALSE' => [false, 'FALSE'];
        yield 'ON' => [true, 'ON'];
        yield 'OFF' => [false, 'OFF'];
        yield 'one' => [true, '1'];
        yield 'zero' => [false, '0'];
        yield 'empty-string' => [false, ''];
        yield 'custom-string' => [false, 'test'];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, string $value): void
    {
        $variableName = 'YII_DEBUG_TEST_ENVIRONMENT_VARIABLE';
        $event = new stdClass();
        $condition = new EnvironmentVariableCondition($variableName);

        putenv("$variableName=$value");

        $this->assertSame($expected, $condition->match($event));
    }

    public function testNonExistVariable(): void
    {
        $event = new stdClass();
        $condition = new EnvironmentVariableCondition('YII_NON_EXIST_VARIABLE');

        $this->assertFalse($condition->match($event));
    }
}
