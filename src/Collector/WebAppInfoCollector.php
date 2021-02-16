<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Profiler\ProfilerInterface;
use Yiisoft\Yii\Web\Event\AfterEmit;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;
use function is_object;

final class WebAppInfoCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private ProfilerInterface $profiler;
    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;
    private float $routeMatchTime = 0;

    public function __construct(ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

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
            [$message] = $this->profiler->findMessages('Matching route');
            if ($message !== null) {
                $this->routeMatchTime = $message->context('duration', 0);
            }
        } elseif ($event instanceof AfterEmit) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }
    }

    public function getIndexData(): array
    {
        return [
            'time' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'memory' => memory_get_peak_usage(),
            'timestamp' => $this->requestProcessingTimeStarted,
            'routeTime' => $this->routeMatchTime,
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
