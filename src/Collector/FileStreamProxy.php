<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Debug\Helper\StreamWrapper\StreamWrapper;
use Yiisoft\Yii\Debug\Helper\StreamWrapper\StreamWrapperInterface;

use const SEEK_SET;

class FileStreamProxy implements StreamWrapperInterface
{
    public static bool $registered = false;
    /**
     * @var resource|null
     */
    public $context;
    public StreamWrapperInterface $decorated;
    public bool $ignored = false;

    public static ?FileStreamCollector $collector = null;
    public static array $ignoredPathPatterns = [];
    public static array $ignoredClasses = [];

    public function __construct()
    {
        $this->decorated = new StreamWrapper();
        $this->decorated->context = $this->context;
    }

    public function __call(string $name, array $arguments)
    {
        try {
            self::unregister();
            return $this->decorated->{$name}(...$arguments);
        } finally {
            self::register();
        }
    }

    public function __get(string $name)
    {
        return $this->decorated->{$name};
    }

    public static function register(): void
    {
        if (self::$registered) {
            return;
        }
        /**
         * It's important to trigger autoloader before unregistering the file stream handler
         */
        class_exists(StreamWrapper::class);
        stream_wrapper_unregister('file');
        stream_wrapper_register('file', self::class, STREAM_IS_URL);
        self::$registered = true;
    }

    public static function unregister(): void
    {
        if (!self::$registered) {
            return;
        }
        @stream_wrapper_restore('file');
        self::$registered = false;
    }

    /**
     * TODO: optimise the check. Maybe a hashmap?
     */
    private function setIgnored(): void
    {
        $backtrace = debug_backtrace();
        /**
         * 0 – Called method
         * 1 – Proxy
         * 2 – Real using place / Composer\ClassLoader include function
         * 3 – Whatever / Composer\ClassLoader
         */
        if (isset($backtrace[3]['class']) && in_array($backtrace[3]['class'], self::$ignoredClasses, true)) {
            $this->ignored = true;
            return;
        }

        if (!isset($backtrace[2])) {
            return;
        }
        $path = $backtrace[2]['file'];

        $result = false;
        foreach (self::$ignoredPathPatterns as $ignoredPathPattern) {
            if (preg_match($ignoredPathPattern, $path) > 0) {
                $result = true;
                break;
            }
        }
        $this->ignored = $result;
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        $this->setIgnored();
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_read(int $count): string|false
    {
        $a = debug_backtrace();
        if (!$this->ignored) {
            self::$collector->collect(
                operation: 'read',
                path: $this->decorated->filename,
                args: [],
            );
        }
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_set_option(int $option, int $arg1, ?int $arg2): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_tell(): int
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_eof(): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_cast(int $castAs)
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_stat(): array
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function dir_closedir(): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function dir_opendir(string $path, int $options): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function dir_readdir(): string
    {
        if (!$this->ignored) {
            self::$collector->collect(
                operation: __FUNCTION__,
                path: $this->decorated->filename,
                args: [],
            );
        }
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function dir_rewinddir(): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function mkdir(string $path, int $mode, int $options): bool
    {
        if (!$this->ignored) {
            self::$collector->collect(
                operation: __FUNCTION__,
                path: $path,
                args: [
                    'mode' => $mode,
                    'options' => $options,
                ]
            );
        }
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function rename(string $path_from, string $path_to): bool
    {
        if (!$this->ignored) {
            self::$collector->collect(
                operation: __FUNCTION__,
                path: $path_from,
                args: [
                    'path_to' => $path_to,
                ],
            );
        }
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function rmdir(string $path, int $options): bool
    {
        if (!$this->ignored) {
            self::$collector->collect(
                operation: __FUNCTION__,
                path: $path,
                args: [
                    'options' => $options,
                ],
            );
        }
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_close(): void
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_flush(): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_lock(int $operation): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_truncate(int $new_size): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function stream_write(string $data): int
    {
        if (!$this->ignored) {
            self::$collector->collect(
                operation: 'write',
                path: $this->decorated->filename,
                args: [],
            );
        }

        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function unlink(string $path): bool
    {
        if (!$this->ignored) {
            self::$collector->collect(
                operation: __FUNCTION__,
                path: $path,
                args: [],
            );
        }
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function url_stat(string $path, int $flags): array|false
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }
}
