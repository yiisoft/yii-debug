<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Yiisoft\Yii\Debug\Helper\BacktraceMatcher;

final class BacktraceMatcherTest extends TestCase
{
    public function testClassIgnorance(): void
    {
        $backtrace = debug_backtrace();

        $this->assertFalse(BacktraceMatcher::matchesClass($backtrace[3], [self::class]));
        $this->assertFalse(BacktraceMatcher::matchesClass($backtrace[3], [stdClass::class]));

        $this->assertTrue(BacktraceMatcher::matchesClass($backtrace[0], [self::class]));
        $this->assertFalse(BacktraceMatcher::matchesClass($backtrace[0], [stdClass::class]));
    }

    public function testFileIgnorance(): void
    {
        $backtrace = debug_backtrace();
        $reflection = new ReflectionClass(TestCase::class);
        $file = $reflection->getFileName();

        $this->assertFalse(BacktraceMatcher::matchesFile($backtrace[2], [preg_quote($file)]));
        $this->assertFalse(BacktraceMatcher::matchesFile($backtrace[2], [preg_quote(__FILE__)]));

        $this->assertTrue(BacktraceMatcher::matchesFile($backtrace[0], [preg_quote($file)]));
        $this->assertTrue(
            BacktraceMatcher::matchesFile(
                $backtrace[0],
                [preg_quote(dirname($file) . DIRECTORY_SEPARATOR) . '*']
            )
        );
        $this->assertFalse(BacktraceMatcher::matchesFile($backtrace[0], [preg_quote(__FILE__)]));
    }

    public function testEmptyBacktrace(): void
    {
        $this->assertFalse(BacktraceMatcher::matchesFile([], ['dev/123/456']));
        $this->assertFalse(BacktraceMatcher::matchesClass([], ['dev/123/456']));
    }
}
