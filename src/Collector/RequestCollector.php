<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;
use function is_object;

final class RequestCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private string $requestUrl = '';
    private string $requestMethod = '';
    private bool $requestIsAjax = false;
    private ?string $userIp = null;
    private int $responseStatusCode = 200;

    public function getCollected(): array
    {
        return [
            'request_url' => $this->requestUrl,
            'request_method' => $this->requestMethod,
            'request_is_ajax' => $this->requestIsAjax,
            'user_ip' => $this->userIp,
            'response_status_code' => $this->responseStatusCode,
        ];
    }

    public function collect(object $event): void
    {
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof BeforeRequest) {
            $this->requestUrl = (string)$event->getRequest()->getUri();
            $this->requestMethod = $event->getRequest()->getMethod();
            $this->requestIsAjax = strtolower(
                $event->getRequest()->getHeaderLine('X-Requested-With') ?? ''
            ) === 'xmlhttprequest';
            $this->userIp = $event->getRequest()->getServerParams()['REMOTE_ADDR'] ?? null;
        }
        if ($event instanceof AfterRequest) {
            $this->responseStatusCode = $event->getResponse()->getStatusCode();
        }
    }

    public function getIndexData(): array
    {
        return [
            'requestUrl' => $this->requestUrl,
            'requestMethod' => $this->requestMethod,
            'requestIsAjax' => $this->requestIsAjax,
            'userIp' => $this->userIp,
            'responseStatusCode' => $this->responseStatusCode,
        ];
    }

    private function reset(): void
    {
        $this->requestUrl = '';
        $this->requestMethod = '';
        $this->requestIsAjax = false;
        $this->userIp = null;
        $this->responseStatusCode = 200;
    }
}
