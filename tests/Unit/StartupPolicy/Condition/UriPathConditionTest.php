<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Condition;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\UriPathCondition;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class UriPathConditionTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'root-match' => [true, ['/'], '/'];
        yield 'root-not-match' => [false, ['/test'], '/'];
        yield 'not-match' => [false, ['/test/run', '/test/stop'], '/blog/post'];
        yield 'wildcard-1' => [true, ['/blog/*'], '/blog/post'];
        yield 'wildcard-2' => [false, ['/blog/*'], '/blog/post/23'];
        yield 'wildcard-3' => [true, ['/blog/**'], '/blog/post/23'];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, array $paths, string $uri): void
    {
        $event = new BeforeRequest(new ServerRequest('GET', $uri));
        $condition = new UriPathCondition($paths);

        $this->assertSame($expected, $condition->match($event));
    }
}
