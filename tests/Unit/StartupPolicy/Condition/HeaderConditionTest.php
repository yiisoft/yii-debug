<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Condition;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\HeaderCondition;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class HeaderConditionTest extends TestCase
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
    public function testBase(bool $expected, string $headerValue): void
    {
        $headerName = 'X-Debug-Ignore';
        $event = new BeforeRequest(new ServerRequest('GET', '/test', [$headerName => $headerValue]));
        $condition = new HeaderCondition($headerName);

        $this->assertSame($expected, $condition->match($event));
    }

    public function testNonExistHeader(): void
    {
        $event = new BeforeRequest(new ServerRequest('GET', '/test', []));
        $condition = new HeaderCondition('X-Debug-Ignore');

        $this->assertFalse($condition->match($event));
    }
}
