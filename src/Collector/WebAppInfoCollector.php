<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\AfterEmit;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;

final class WebAppInfoCollector implements CollectorInterface
{
    use CollectorTrait;

    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;
    private string $requestUrl = '';
    private string $requestMethod = '';
    private bool $requestIsAjax = false;
    private ?string $userIp = null;
    private int $responseStatusCode = 200;

    public function getCollected(): array
    {
        return [
            'application_processing_time' => $this->applicationProcessingTimeStopped - $this->applicationProcessingTimeStarted,
            'application_preload' => $this->requestProcessingTimeStarted - $this->applicationProcessingTimeStarted,
            'request_processing_time' => $this->requestProcessingTimeStopped - $this->requestProcessingTimeStarted,
            'application_emit' => $this->applicationProcessingTimeStopped - $this->requestProcessingTimeStopped,
            'memory_peak_usage' => memory_get_peak_usage(true),
            'memory_usage' => memory_get_usage(true),
            'request_url' => $this->requestUrl,
            'request_method' => $this->requestMethod,
            'request_is_ajax' => $this->requestIsAjax,
            'user_ip' => $this->userIp,
            'response_status_code' => $this->responseStatusCode
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
            $this->requestUrl = (string)$event->getRequest()->getUri();
            $this->requestMethod = $event->getRequest()->getMethod();
            $this->requestIsAjax = $event->getRequest()->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
            $this->userIp = $event->getRequest()->getServerParams()['REMOTE_ADDR'] ?? null;
        } elseif ($event instanceof AfterRequest) {
            $this->requestProcessingTimeStopped = microtime(true);
            $this->responseStatusCode = $event->getResponse()->getStatusCode();
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
        $this->requestUrl = '';
        $this->requestMethod = '';
        $this->requestIsAjax = false;
        $this->userIp = null;
        $this->responseStatusCode = 200;
    }
}
