<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;
use Yiisoft\Yii\Web\Event\ApplicationStartup;
use Yiisoft\Yii\Web\Event\BeforeRequest;

final class RequestCollector implements CollectorInterface
{
    use CollectorTrait;

    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;

    public function collected(): array
    {
        return [
            'application_processing_time' => $this->applicationProcessingTimeStopped - $this->applicationProcessingTimeStarted,
            'request_processing_time' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'memory_peak_usage' => memory_get_peak_usage(true),
            'memory_usage' => memory_get_usage(true),
        ];
    }

    public function collect(...$payload): void
    {
        $event = current($payload);
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof ApplicationStartup) {
            $this->applicationProcessingTimeStarted = microtime(true);
        } elseif ($event instanceof BeforeRequest) {
            $this->requestProcessingTimeStarted = microtime(true);
        } elseif ($event instanceof AfterRequest) {
            $this->requestProcessingTimeStopped = microtime(true);
        } elseif ($event instanceof ApplicationShutdown) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }
    }
}
