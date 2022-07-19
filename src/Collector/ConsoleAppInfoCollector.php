<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Yiisoft\Yii\Console\Event\ApplicationShutdown;
use Yiisoft\Yii\Console\Event\ApplicationStartup;

final class ConsoleAppInfoCollector implements CollectorInterface, IndexCollectorInterface
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
            'preloadTime' => $this->requestProcessingTimeStarted - $this->applicationProcessingTimeStarted,
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
        } elseif ($event instanceof ConsoleTerminateEvent) {
            $this->requestProcessingTimeStopped = microtime(true);
        } elseif ($event instanceof ApplicationShutdown) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }
    }

    public function getIndexData(): array
    {
        return [
            'console.php.version' => PHP_VERSION,
            'console.request.startTime' => $this->requestProcessingTimeStarted,
            'console.request.processingTime' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'console.memory.peakUsage' => memory_get_peak_usage(),
        ];
    }

    private function reset(): void
    {
        $this->applicationProcessingTimeStarted = 0;
        $this->applicationProcessingTimeStopped = 0;
    }
}
