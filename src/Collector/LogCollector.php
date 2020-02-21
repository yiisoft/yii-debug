<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class LogCollector implements CollectorInterface, LoggerInterface
{
    use CollectorTrait;

    private LoggerInterface $logger;
    private array $messages = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function collect(): array
    {
        return $this->messages;
    }

    public function emergency($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::EMERGENCY, $message, $context);
        }
        $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::ALERT, $message, $context);
        }
        $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::CRITICAL, $message, $context);
        }
        $this->logger->critical($message, $context);
    }

    public function error($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::ERROR, $message, $context);
        }
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::WARNING, $message, $context);
        }
        $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::NOTICE, $message, $context);
        }
        $this->logger->notice($message, $context);
    }

    public function info($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::INFO, $message, $context);
        }
        $this->logger->info($message, $context);
    }

    public function debug($message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages(LogLevel::DEBUG, $message, $context);
        }
        $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        if ($this->isActive()) {
            $this->collectMessages($level, $message, $context);
        }
        $this->logger->log($message, $context);
    }

    private function collectMessages(string $level, string $message, array $context): void
    {
        $this->messages[] = [
            'time' => microtime(true),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }
}
