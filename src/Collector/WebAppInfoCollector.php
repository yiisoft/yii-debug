<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\AfterEmit;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;

final class WebAppInfoCollector implements CollectorInterface, IndexCollectorInterface
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
            'application_preload' => $this->requestProcessingTimeStarted - $this->applicationProcessingTimeStarted,
            'request_processing_time' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'application_emit' => $this->applicationProcessingTimeStopped - $this->requestProcessingTimeStopped,
            'memory_peak_usage' => memory_get_peak_usage(),
            'memory_usage' => memory_get_usage(),
        ];
    }

    public function collect(object $event): void
    {
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof BeforeRequest) {
            $this->requestProcessingTimeStarted = microtime(true);
            $this->applicationProcessingTimeStarted = $event->getRequest()->getAttribute(
                'applicationStartTime',
                $this->requestProcessingTimeStarted
            );
        } elseif ($event instanceof AfterRequest) {
            $this->requestProcessingTimeStopped = microtime(true);
        } elseif ($event instanceof AfterEmit) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }
    }

    private function reset(): void
    {
        $this->applicationProcessingTimeStarted = 0;
        $this->applicationProcessingTimeStopped = 0;
        $this->requestProcessingTimeStarted = 0;
        $this->requestProcessingTimeStopped = 0;
    }

    public function getIndexData(): array
    {
        return [
            'time' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'memory' => memory_get_peak_usage(),
            'timestamp' => $this->requestProcessingTimeStarted,
        ];
    }
}
