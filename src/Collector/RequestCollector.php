<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Http\Event\AfterRequest;
use Yiisoft\Yii\Http\Event\BeforeRequest;

use function is_object;

final class RequestCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private string $requestUrl = '';
    private string $requestPath = '';
    private string $requestQuery = '';
    private string $requestMethod = '';
    private bool $requestIsAjax = false;
    private ?string $userIp = null;
    private int $responseStatusCode = 200;
    private ?ServerRequestInterface $request = null;
    private ?ResponseInterface $response = null;

    public function getCollected(): array
    {
        return [
            'requestUrl' => $this->requestUrl,
            'requestPath' => $this->requestPath,
            'requestQuery' => $this->requestQuery,
            'requestMethod' => $this->requestMethod,
            'requestIsAjax' => $this->requestIsAjax,
            'userIp' => $this->userIp,
            'responseStatusCode' => $this->responseStatusCode,
            'request' => $this->request,
            'response' => $this->response,
        ];
    }

    public function collect(object $event): void
    {
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof BeforeRequest) {
            $this->request = $event->getRequest();
            $this->requestUrl = (string) $event->getRequest()->getUri();
            $this->requestPath = (string) $event->getRequest()->getUri()->getPath();
            $this->requestQuery = (string) $event->getRequest()->getUri()->getQuery();
            $this->requestMethod = $event->getRequest()->getMethod();
            $this->requestIsAjax = strtolower(
                $event->getRequest()->getHeaderLine('X-Requested-With') ?? ''
            ) === 'xmlhttprequest';
            $this->userIp = $event->getRequest()->getServerParams()['REMOTE_ADDR'] ?? null;
        }
        if ($event instanceof AfterRequest) {
            $this->response = $event->getResponse();
            $this->responseStatusCode = $event->getResponse() !== null ? $event->getResponse()->getStatusCode() : 500;
        }
    }

    public function getIndexData(): array
    {
        return [
            'request' => [
                'url' => $this->requestUrl,
                'path' => $this->requestPath,
                'query' => $this->requestQuery,
                'method' => $this->requestMethod,
                'isAjax' => $this->requestIsAjax,
                'userIp' => $this->userIp,
            ],
            'response' => [
                'statusCode' => $this->responseStatusCode,
            ],
        ];
    }

    private function reset(): void
    {
        $this->request = null;
        $this->response = null;
        $this->requestUrl = '';
        $this->requestMethod = '';
        $this->requestIsAjax = false;
        $this->userIp = null;
        $this->responseStatusCode = 200;
    }
}
