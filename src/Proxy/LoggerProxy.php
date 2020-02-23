<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

final class LoggerProxy implements LoggerInterface
{
    private LoggerInterface $logger;
    private CollectorInterface $collector;

    public function __construct(LoggerInterface $logger, CollectorInterface $collector)
    {
        $this->logger = $logger;
        $this->collector = $collector;
    }

    public function emergency($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::EMERGENCY, $message, $context);
        $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::ALERT, $message, $context);
        $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::CRITICAL, $message, $context);
        $this->logger->critical($message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::ERROR, $message, $context);
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::WARNING, $message, $context);
        $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::NOTICE, $message, $context);
        $this->logger->notice($message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::INFO, $message, $context);
        $this->logger->info($message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->collector->dispatch(LogLevel::DEBUG, $message, $context);
        $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $this->collector->dispatch($level, $message, $context);
        $this->logger->log($message, $context);
    }
}
