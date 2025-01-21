<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Yiisoft\Yii\Debug\Helper\BacktraceIgnoreMatcher;

final class BacktraceIgnoreMatcherTest extends TestCase
{
    public function testClassIgnorance(): void
    {
        $backtrace = debug_backtrace();

        $this->assertFalse(BacktraceIgnoreMatcher::matchesClass($backtrace[3], [self::class]));
        $this->assertFalse(BacktraceIgnoreMatcher::matchesClass($backtrace[3], [stdClass::class]));

        $this->assertTrue(BacktraceIgnoreMatcher::matchesClass($backtrace[0], [self::class]));
        $this->assertFalse(BacktraceIgnoreMatcher::matchesClass($backtrace[0], [stdClass::class]));
    }

    public function testFileIgnorance(): void
    {
        $backtrace = debug_backtrace();
        $reflection = new ReflectionClass(TestCase::class);
        $file = $reflection->getFileName();

        $this->assertFalse(BacktraceIgnoreMatcher::matchesFile($backtrace[2], [preg_quote($file)]));
        $this->assertFalse(BacktraceIgnoreMatcher::matchesFile($backtrace[2], [preg_quote(__FILE__)]));

        $this->assertTrue(BacktraceIgnoreMatcher::matchesFile($backtrace[0], [preg_quote($file)]));
        $this->assertTrue(
            BacktraceIgnoreMatcher::matchesFile(
                $backtrace[0],
                [preg_quote(dirname($file) . DIRECTORY_SEPARATOR) . '*']
            )
        );
        $this->assertFalse(BacktraceIgnoreMatcher::matchesFile($backtrace[0], [preg_quote(__FILE__)]));
    }

    public function testEmptyBacktrace(): void
    {
        $this->assertFalse(BacktraceIgnoreMatcher::matchesFile([], ['dev/123/456']));
        $this->assertFalse(BacktraceIgnoreMatcher::matchesClass([], ['dev/123/456']));
    }
}
