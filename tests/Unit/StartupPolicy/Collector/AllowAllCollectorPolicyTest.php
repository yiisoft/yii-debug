<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Collector;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Collector\AllowAllCollectorPolicy;
use Yiisoft\Yii\Debug\Tests\Support\StubCollector;

final class AllowAllCollectorPolicyTest extends TestCase
{
    public function testBase(): void
    {
        $event = new stdClass();
        $collector = new StubCollector();
        $policy = new AllowAllCollectorPolicy();

        $this->assertTrue($policy->satisfies($collector, $event));
    }
}
