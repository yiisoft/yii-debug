<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class HttpClientInterfaceProxy implements ClientInterface
{
    public function __construct(private ClientInterface $decorated, private HttpClientCollector $collector)
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect($request, $callStack['file'] . ':' . $callStack['line']);

        try {
            return $this->decorated->sendRequest($request);
        } finally {
            $this->collector->collectTotalTime($request);
        }
    }
}
