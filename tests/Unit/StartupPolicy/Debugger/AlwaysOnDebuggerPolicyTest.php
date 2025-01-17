<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\StartupPolicy\Debugger;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Yii\Debug\StartupPolicy\Debugger\AlwaysOnDebuggerPolicy;

final class AlwaysOnDebuggerPolicyTest extends TestCase
{
    public function testBase(): void
    {
        $event = new stdClass();
        $policy = new AlwaysOnDebuggerPolicy();

        $this->assertTrue($policy->satisfies($event));
    }
}
