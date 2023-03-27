<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Console;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Yiisoft\Yii\Console\Event\ApplicationShutdown;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

final class ConsoleAppInfoCollector implements IndexCollectorInterface
{
    use CollectorTrait;

    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;

    public function getCollected(): array
    {
        return [
            'applicationProcessingTime' => $this->applicationProcessingTimeStopped - $this->applicationProcessingTimeStarted,
            'preloadTime' => $this->applicationProcessingTimeStarted - $this->requestProcessingTimeStarted,
            'applicationEmit' => $this->applicationProcessingTimeStopped - $this->requestProcessingTimeStopped,
            'requestProcessingTime' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'memoryPeakUsage' => memory_get_peak_usage(),
            'memoryUsage' => memory_get_usage(),
        ];
    }

    public function collect(object $event): void
    {
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof ApplicationStartup) {
            $this->applicationProcessingTimeStarted = microtime(true);
        } elseif ($event instanceof ConsoleCommandEvent) {
            $this->requestProcessingTimeStarted = microtime(true);
        } elseif ($event instanceof ConsoleErrorEvent) {
            /**
             * If we receive this event, then {@see ConsoleCommandEvent} hasn't received and won't.
             * So {@see requestProcessingTimeStarted} equals to 0 now and better to set it at least with application startup time.
             */
            $this->requestProcessingTimeStarted = $this->applicationProcessingTimeStarted;
            $this->requestProcessingTimeStopped = microtime(true);
        } elseif ($event instanceof ConsoleTerminateEvent) {
            $this->requestProcessingTimeStopped = microtime(true);
        } elseif ($event instanceof ApplicationShutdown) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }
    }

    public function getIndexData(): array
    {
        return [
            'console' => [
                'php' => [
                    'version' => PHP_VERSION,
                ],
                'request' => [
                    'startTime' => $this->requestProcessingTimeStarted,
                    'processingTime' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
                ],
                'memory' => [
                    'peakUsage' => memory_get_peak_usage(),
                ],
            ],
        ];
    }

    private function reset(): void
    {
        $this->applicationProcessingTimeStarted = 0;
        $this->applicationProcessingTimeStopped = 0;
    }
}
