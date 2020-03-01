<?php

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Yiisoft\Yii\Debug\Collector\LogCollectorInterface;
use Yiisoft\Yii\Debug\Proxy\LoggerInterfaceProxy;

final class LoggerProxyTest extends TestCase
{
    /**
     * @dataProvider logMethodsProvider()
     */
    public function testLogMethods(string $method, string $level, string $message, array $context): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $collector = $this->createMock(LogCollectorInterface::class);
        $collector
            ->expects($this->once())
            ->method('collect')
            ->with($level, $message, $context);
        $proxy = new LoggerInterfaceProxy($logger, $collector);

        $proxy->$method($message, $context);
    }

    /**
     * @dataProvider logMethodsProvider()
     */
    public function testMethodLog($method, string $level, string $message, array $context): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $collector = $this->createMock(LogCollectorInterface::class);
        $collector
            ->expects($this->once())
            ->method('collect')
            ->with($level, $message, $context);
        $proxy = new LoggerInterfaceProxy($logger, $collector);

        $proxy->log($level, $message, $context);
    }

    public function logMethodsProvider(): array
    {
        return [
            ['alert', LogLevel::ALERT, 'message', []],
            ['critical', LogLevel::CRITICAL, 'message', []],
            ['debug', LogLevel::DEBUG, 'message', []],
            ['emergency', LogLevel::EMERGENCY, 'message', []],
            ['error', LogLevel::ERROR, 'message', ['context']],
            ['info', LogLevel::INFO, 'message', ['context']],
            ['warning', LogLevel::WARNING, 'message', ['context']],
        ];
    }
}
