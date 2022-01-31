<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Yii\Console\Event\ApplicationShutdown;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Http\Event\AfterEmit;
use Yiisoft\Yii\Http\Event\AfterRequest;
use Yiisoft\Yii\Http\Event\BeforeRequest;

use function is_object;

final class WebAppInfoCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;

    #[ArrayShape([
        'applicationProcessingTime' => "float|int",
        'applicationPreload' => "float|int",
        'requestProcessingTime' => "float|int",
        'applicationEmit' => "float|int",
        'memoryPeakUsage' => "int",
        'memoryUsage' => "int"
    ])]
    public function getCollected(): array
    {
        return [
            'applicationProcessingTime' => $this->applicationProcessingTimeStopped - $this->applicationProcessingTimeStarted,
            'applicationPreload' => $this->requestProcessingTimeStarted - $this->applicationProcessingTimeStarted,
            'requestProcessingTime' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'applicationEmit' => $this->applicationProcessingTimeStopped - $this->requestProcessingTimeStopped,
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

    #[ArrayShape(['phpVersion' => "string", 'time' => "float|int", 'memory' => "int", 'timestamp' => "float|int"])]
    public function getIndexData(): array
    {
        return [
            'phpVersion' => PHP_VERSION,
            'time' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'memory' => memory_get_peak_usage(),
            'timestamp' => $this->requestProcessingTimeStarted,
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
