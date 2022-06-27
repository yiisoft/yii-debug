<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;

final class LoggerInterfaceProxy implements LoggerInterface
{
    private LoggerInterface $logger;
    private LogCollectorInterface $collector;

    public function __construct(LoggerInterface $logger, LogCollectorInterface $collector)
    {
        $this->logger = $logger;
        $this->collector = $collector;
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(
            LogLevel::EMERGENCY,
            $message,
            $context,
            $callStack['file'] . ':' . $callStack['line']
        );
        $this->logger->emergency($message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(LogLevel::ALERT, $message, $context, $callStack['file'] . ':' . $callStack['line']);
        $this->logger->alert($message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(
            LogLevel::CRITICAL,
            $message,
            $context,
            $callStack['file'] . ':' . $callStack['line']
        );
        $this->logger->critical($message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(LogLevel::ERROR, $message, $context, $callStack['file'] . ':' . $callStack['line']);
        $this->logger->error($message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(LogLevel::WARNING, $message, $context, $callStack['file'] . ':' . $callStack['line']);
        $this->logger->warning($message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(LogLevel::NOTICE, $message, $context, $callStack['file'] . ':' . $callStack['line']);
        $this->logger->notice($message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(LogLevel::INFO, $message, $context, $callStack['file'] . ':' . $callStack['line']);
        $this->logger->info($message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect(LogLevel::DEBUG, $message, $context, $callStack['file'] . ':' . $callStack['line']);
        $this->logger->debug($message, $context);
    }

    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect($level, $message, $context, $callStack['file'] . ':' . $callStack['line']);
        $this->logger->log($level, $message, $context);
    }
}
