<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;

final class RequestCollector implements CollectorInterface
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
            'response_status_code' => $this->responseStatusCode
        ];
    }

    public function collect(object $event): void
    {
        if ($event instanceof BeforeRequest) {
            $this->requestUrl = (string)$event->getRequest()->getUri();
            $this->requestMethod = $event->getRequest()->getMethod();
            $this->requestIsAjax = $event->getRequest()->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
            $this->userIp = $event->getRequest()->getServerParams()['REMOTE_ADDR'] ?? null;
        }
        if ($event instanceof AfterRequest) {
            $this->responseStatusCode = $event->getResponse()->getStatusCode();
        }
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
