<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JsonException;
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
        $body = null;
        if ($this->response !== null) {
            $stream = $this->response->getBody();
            if ($stream->isReadable() && $stream->isSeekable()) {
                $position = $stream->tell();
                $stream->rewind();
                $body = $stream->getContents();
                try {
                    $body = json_decode($body, associative: true, flags: JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                    // pass
                }
                $stream->seek($position);
            }
        }

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
            'responseRaw' => $body,
        ];
    }

    public function collect(object $event): void
    {
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof BeforeRequest) {
            $request = $event->getRequest();

            $this->request = $request;
            $this->requestUrl = (string) $request->getUri();
            $this->requestPath = $request->getUri()->getPath();
            $this->requestQuery = $request->getUri()->getQuery();
            $this->requestMethod = $request->getMethod();
            $this->requestIsAjax = strtolower($request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest';
            $this->userIp = $request->getServerParams()['REMOTE_ADDR'] ?? null;
        }
        if ($event instanceof AfterRequest) {
            $response = $event->getResponse();

            $this->response = $response;
            $this->responseStatusCode = $response !== null ? $response->getStatusCode() : 500;
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
