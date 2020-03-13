<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
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

    public function emergency($message, array $context = [])
    {
        $this->collector->collect(LogLevel::EMERGENCY, $message, $context);
        $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->collector->collect(LogLevel::ALERT, $message, $context);
        $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->collector->collect(LogLevel::CRITICAL, $message, $context);
        $this->logger->critical($message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->collector->collect(LogLevel::ERROR, $message, $context);
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->collector->collect(LogLevel::WARNING, $message, $context);
        $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->collector->collect(LogLevel::NOTICE, $message, $context);
        $this->logger->notice($message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->collector->collect(LogLevel::INFO, $message, $context);
        $this->logger->info($message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->collector->collect(LogLevel::DEBUG, $message, $context);
        $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $this->collector->collect($level, $message, $context);
        $this->logger->log($level, $message, $context);
    }
}
