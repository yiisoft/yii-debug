<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Yiisoft\Yii\Debug\Collector\LogCollector;
use Yiisoft\Yii\Debug\Collector\LoggerInterfaceProxy;

final class LoggerInterfaceProxyTest extends TestCase
{
    #[DataProvider('logMethodsProvider')]
    public function testLogMethods(string $method, string $level, string $message, array $context): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $collector = $this->createMock(LogCollector::class);
        $collector
            ->expects($this->once())
            ->method('collect')
            ->with($level, $message, $context, __FILE__ . ':27');
        $proxy = new LoggerInterfaceProxy($logger, $collector);

        $proxy->$method($message, $context);
    }

    #[DataProvider('logMethodsProvider')]
    public function testMethodLog($method, string $level, string $message, array $context): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $collector = $this->createMock(LogCollector::class);
        $collector
            ->expects($this->once())
            ->method('collect')
            ->with($level, $message, $context, __FILE__ . ':41');
        $proxy = new LoggerInterfaceProxy($logger, $collector);

        $proxy->log($level, $message, $context);
    }

    public static function logMethodsProvider(): iterable
    {
        yield 'alert' => ['alert', LogLevel::ALERT, 'message', []];
        yield 'critical' => ['critical', LogLevel::CRITICAL, 'message', []];
        yield 'debug' => ['debug', LogLevel::DEBUG, 'message', []];
        yield 'emergency' => ['emergency', LogLevel::EMERGENCY, 'message', []];
        yield 'notice' => ['notice', LogLevel::NOTICE, 'message', []];
        yield 'error' => ['error', LogLevel::ERROR, 'message', ['context']];
        yield 'info' => ['info', LogLevel::INFO, 'message', ['context']];
        yield 'warning' => ['warning', LogLevel::WARNING, 'message', ['context']];
    }
}
