<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Web;

use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Http\Event\AfterEmit;
use Yiisoft\Yii\Http\Event\AfterRequest;
use Yiisoft\Yii\Http\Event\BeforeRequest;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;

use function is_object;

final class WebAppInfoCollector implements IndexCollectorInterface
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
            'requestProcessingTime' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'applicationEmit' => $this->applicationProcessingTimeStopped - $this->requestProcessingTimeStopped,
            'preloadTime' => $this->requestProcessingTimeStarted - $this->applicationProcessingTimeStarted,
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
        } elseif ($event instanceof BeforeRequest) {
            $this->requestProcessingTimeStarted = microtime(true);
        } elseif ($event instanceof AfterRequest) {
            $this->requestProcessingTimeStopped = microtime(true);
        } elseif ($event instanceof AfterEmit) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }
    }

    public function getIndexData(): array
    {
        return [
            'web' => [
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
        $this->requestProcessingTimeStarted = 0;
        $this->requestProcessingTimeStopped = 0;
    }
}
