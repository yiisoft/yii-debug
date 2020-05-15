<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Console\Event\ApplicationShutdown;
use Yiisoft\Yii\Console\Event\ApplicationStartup;

final class ConsoleAppInfoCollector implements CollectorInterface
{
    use CollectorTrait;

    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;

    public function getCollected(): array
    {
        return [
            'application_processing_time' => $this->applicationProcessingTimeStopped - $this->applicationProcessingTimeStarted,
            'memory_peak_usage' => memory_get_peak_usage(true),
            'memory_usage' => memory_get_usage(true),
        ];
    }

    public function collect(object $event): void
    {
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof ApplicationStartup) {
            $this->applicationProcessingTimeStarted = microtime(true);
        } elseif ($event instanceof ApplicationShutdown) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }
    }

    private function reset(): void
    {
        $this->applicationProcessingTimeStarted = 0;
        $this->applicationProcessingTimeStopped = 0;
    }
}
