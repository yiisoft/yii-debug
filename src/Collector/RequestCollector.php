<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;
use Yiisoft\Yii\Web\Event\ApplicationStartup;
use Yiisoft\Yii\Web\Event\BeforeRequest;

final class RequestCollector implements CollectorInterface, EventDispatcherInterface
{
    use CollectorTrait;

    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;

    public function collect(): array
    {
        return [
            'application_processing_time' => $this->applicationProcessingTimeStopped - $this->applicationProcessingTimeStarted,
            'request_processing_time' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'memory_peak_usage' => memory_get_peak_usage(true),
            'memory_usage' => memory_get_usage(true),
        ];
    }

    public function dispatch(object $event)
    {
        if ($this->isActive()) {
            if ($event instanceof BeforeRequest) {
                $this->requestProcessingTimeStarted = microtime(true);
            } elseif ($event instanceof AfterRequest) {
                $this->requestProcessingTimeStopped = microtime(true);
            } elseif ($event instanceof ApplicationStartup) {
                $this->applicationProcessingTimeStarted = microtime(true);
            } elseif ($event instanceof ApplicationShutdown) {
                $this->applicationProcessingTimeStopped = microtime(true);
            }
        }

        return $event;
    }
}
