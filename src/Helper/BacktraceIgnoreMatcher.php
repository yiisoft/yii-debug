<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Helper;

/**
 * All backtrace parameters should contain at least 4 elements in the following order:
 * 0 – Called method
 * 1 – Proxy
 * 2 – Real using place / Composer\ClassLoader include function
 * 3 – Whatever / Composer\ClassLoader
 */
final class BacktraceIgnoreMatcher
{
    public static function isIgnoredByFile(array $backtrace, array $patterns): bool
    {
        if (!isset($backtrace[2])) {
            return false;
        }
        $path = $backtrace[2]['file'];

        return self::doesStringMatchPattern($path, $patterns);
    }

    public static function isIgnoredByClass(array $backtrace, array $classes): bool
    {
        return (bool) (isset($backtrace[3]['class']) && in_array($backtrace[3]['class'], $classes, true))


         ;
    }

    public static function doesStringMatchPattern(string $string, array $patterns): bool
    {
        $result = false;
        foreach ($patterns as $ignoredPathPattern) {
            if (preg_match($ignoredPathPattern, $string) > 0) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
