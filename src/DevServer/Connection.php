<?php
/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\DevServer;

use Closure;
use RuntimeException;
use Socket;
use Throwable;

final class Connection
{
    public const DEFAULT_SOCKET_DIR = '/tmp/var-dumper';
    public const DEFAULT_SOCKET_URL = '/tmp/var-dumper-%d.sock';

    public function __construct(
        private Socket $socket,
    )
    {
    }

    public static function create(): self
    {
        $socket = socket_create(AF_UNIX, SOCK_DGRAM, 0);

        return new self(
            $socket,
        );
    }

    public function bind(): void
    {
        $n = random_int(0, PHP_INT_MAX);
        $file = sprintf(self::DEFAULT_SOCKET_URL, $n);
        if (!socket_bind($this->socket, $file)) {
            $socket_last_error = socket_last_error($this->socket);

            throw new RuntimeException(
                sprintf(
                    'An error occurred while reading the socket. "socket_last_error" returned %d: "%s".',
                    $socket_last_error,
                    socket_strerror($socket_last_error),
                ),
            );
        }
    }

    public function broadcast(string $data): void
    {
        $files = glob(self::DEFAULT_SOCKET_DIR . '-*.sock', GLOB_NOSORT);
        //echo 'Files: ' . implode(', ', $files) . "\n";
        foreach ($files as $file) {
            $socket = socket_create(AF_UNIX, SOCK_DGRAM, 0);
            if (!@socket_connect($socket, $file)) {
                @unlink($file);
                continue;
            }
            try {
                socket_send($socket, $data, strlen($data), 0);
            } catch (Throwable $e) {
                unlink($file);
                throw $e;
            } finally {
                socket_close($socket);
            }
        }
    }

    public function close(): void
    {
        @socket_getsockname($this->socket, $path);
        @socket_close($this->socket);
        @unlink($path);
    }

    public function read(Closure $onSuccess, ?Closure $onError = null): \Generator
    {
        while (true) {
            if (!socket_recvfrom($this->socket, $buffer, 32768, MSG_DONTWAIT, $ip, $port)) {
                $socket_last_error = socket_last_error($this->socket);
                if ($socket_last_error === 35) {
                    continue;
                }
                $this->close();
                if ($onError !== null) {
                    yield $onError($socket_last_error);
                    break;
                }
                throw new RuntimeException(
                    sprintf(
                        'An error occurred while reading the socket. socket_last_error returned %d: "%s".',
                        $socket_last_error,
                        socket_strerror($socket_last_error)
                    ),
                );
            }
            yield $onSuccess($buffer, $ip, $port);
        }
    }
}
