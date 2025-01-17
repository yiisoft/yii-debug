<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Condition;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\CommandNameCondition;

final class CommandNameConditionTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'without-name' => [false, ['test:run', 'test:stop'], null];
        yield 'with-empty-name' => [false, ['test:run', 'test:stop'], ''];
        yield 'not-match' => [false, ['test:run', 'test:stop'], 'email:send'];
        yield 'match' => [true, ['test:run', 'test:stop'], 'test:stop'];
        yield 'match-wildcard-1' => [true, ['test:app:*'], 'test:app:stop'];
        yield 'match-wildcard-2' => [false, ['test:*'], 'test:app:stop'];
        yield 'match-wildcard-3' => [true, ['test:**'], 'test:app:stop'];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, array $names, ?string $currentName): void
    {
        $event = new ApplicationStartup($currentName);
        $condition = new CommandNameCondition($names);

        $this->assertSame($expected, $condition->match($event));
    }
}
