<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Collector;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Collector\BlackListCollectorPolicy;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\PredefinedCondition;
use Yiisoft\Yii\Debug\Tests\Support\StubCollector;

final class BlackListCollectorPolicyTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'empty' => [true, []];
        yield 'collector-not-exist' => [true, ['other' => new PredefinedCondition(true)]];
        yield 'collector-true' => [false, ['test' => new PredefinedCondition(true)]];
        yield 'collector-false' => [true, ['test' => new PredefinedCondition(false)]];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, array $conditions): void
    {
        $event = new stdClass();
        $collector = new StubCollector('test');
        $policy = new BlackListCollectorPolicy($conditions);

        $this->assertSame($expected, $policy->satisfies($collector, $event));
    }
}
