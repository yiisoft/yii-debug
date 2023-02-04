<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Helper\StreamWrapper;

use Throwable;

use function trigger_error;

use const E_USER_ERROR;
use const STREAM_MKDIR_RECURSIVE;
use const STREAM_URL_STAT_QUIET;
use const STREAM_USE_PATH;

final class StreamWrapper implements StreamWrapperInterface
{
    /**
     * @var resource|null
     */
    public mixed $context = null;

    public ?string $filename = null;

    /**
     * @var resource|null
     */
    public $stream = null;

    public function dir_closedir(): bool
    {
        return true;
    }

    public function dir_opendir(string $path, int $options): bool
    {
        return true;
    }

    public function dir_readdir(): string
    {
        return readdir($this->stream);
    }

    public function dir_rewinddir(): bool
    {
        return rewinddir($this->stream);
    }

    public function mkdir(string $path, int $mode, int $options): bool
    {
        return mkdir($path, $mode, ($options & STREAM_MKDIR_RECURSIVE) === STREAM_MKDIR_RECURSIVE);
    }

    public function rename(string $path_from, string $path_to): bool
    {
        return rename($path_from, $path_to);
    }

    public function rmdir(string $path, int $options): bool
    {
        return rmdir($path);
    }

    public function stream_cast(int $cast_as)
    {
        //????
    }

    public function stream_eof(): bool
    {
        return feof($this->stream);
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool
    {
        $this->filename = realpath($path) ?: $path;
        $this->stream = fopen($path, $mode, ($options & STREAM_USE_PATH) === STREAM_USE_PATH);

        return $this->stream !== false;
    }

    public function stream_read(int $count): string|false
    {
        return is_readable($this->filename) ? fread($this->stream, $count) : false;
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        return fseek($this->stream, $offset, $whence) === 0;
    }

    public function stream_set_option(int $option, int $arg1, ?int $arg2): bool
    {
        return match ($option) {
            STREAM_OPTION_BLOCKING => stream_set_blocking($this->stream, $arg1 === STREAM_OPTION_BLOCKING),
            STREAM_OPTION_READ_TIMEOUT => stream_set_timeout($this->stream, $arg1, $arg2),
            STREAM_OPTION_WRITE_BUFFER => stream_set_write_buffer($this->stream, $arg2) === 0,
            default => false,
        };
    }

    public function stream_stat(): array
    {
        return fstat($this->stream);
    }

    public function stream_tell(): int
    {
        return ftell($this->stream);
    }

    public function stream_write(string $data): int
    {
        return fwrite($this->stream, $data);
    }

    public function url_stat(string $path, int $flags): array|false
    {
        try {
            return stat($path);
        } catch (Throwable $e) {
            if (($flags & STREAM_URL_STAT_QUIET) === STREAM_URL_STAT_QUIET) {
                return false;
            }
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        return false;
    }

    public function stream_metadata(string $path, int $option, mixed $value): bool
    {
        return match ($option) {
            STREAM_META_TOUCH => touch($path, ...$value),
            STREAM_META_OWNER_NAME, STREAM_META_OWNER => chown($path, $value),
            STREAM_META_GROUP_NAME, STREAM_META_GROUP => chgrp($path, $value),
            STREAM_META_ACCESS => chmod($path, $value),
            default => false
        };
    }

    public function stream_flush(): bool
    {
        return fflush($this->stream);
    }

    public function stream_close(): void
    {
        fclose($this->stream);
        $this->stream = null;
    }

    public function stream_lock(int $operation): bool
    {
        return flock($this->stream, $operation);
    }

    public function stream_truncate(int $new_size): bool
    {
        return ftruncate($this->stream, $new_size);
    }

    public function unlink(string $path): bool
    {
        return unlink($path, $this->context);
    }
}
