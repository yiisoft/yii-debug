<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Collector;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Collector\WhiteListCollectorPolicy;
use Yiisoft\Yii\Debug\StartupPolicy\Condition\PredefinedCondition;
use Yiisoft\Yii\Debug\Tests\Support\StubCollector;

final class WhiteListCollectorPolicyTest extends TestCase
{
    public static function dataBase(): iterable
    {
        yield 'empty' => [false, []];
        yield 'collector-not-exist' => [false, ['other' => new PredefinedCondition(true)]];
        yield 'collector-true' => [true, ['test' => new PredefinedCondition(true)]];
        yield 'collector-false' => [false, ['test' => new PredefinedCondition(false)]];
    }

    #[DataProvider('dataBase')]
    public function testBase(bool $expected, array $conditions): void
    {
        $event = new stdClass();
        $collector = new StubCollector('test');
        $policy = new WhiteListCollectorPolicy($conditions);

        $this->assertSame($expected, $policy->satisfies($collector, $event));
    }
}
