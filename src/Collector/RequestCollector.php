<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Debug\Event\RequestEndEvent;
use Yiisoft\Yii\Debug\Event\RequestStartedEvent;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;
use Yiisoft\Yii\Web\Event\ApplicationStartup;
use Yiisoft\Yii\Web\Event\BeforeRequest;

class RequestCollector implements CollectorInterface, EventDispatcherInterface
{
    use CollectorTrait;

    private ?ServerRequestInterface $request = null;
    private ?ResponseInterface $response = null;
    private EventDispatcherInterface $eventDispatcher;
    private float $applicationProcessingTimeStarted = 0;
    private float $applicationProcessingTimeStopped = 0;
    private float $requestProcessingTimeStarted = 0;
    private float $requestProcessingTimeStopped = 0;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function collect(): array
    {
        return [
            $this->request,
            $this->response,
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
            }
        }

        if ($event instanceof ApplicationStartup) {
            $this->applicationProcessingTimeStarted = microtime(true);
        } elseif ($event instanceof ApplicationShutdown) {
            $this->applicationProcessingTimeStopped = microtime(true);
        }

        return $this->eventDispatcher->dispatch($event);
    }
}
