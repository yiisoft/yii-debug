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

        $uniqueId = random_bytes(36);
        $startTime = microtime(true);
        $this->collector->collect($request, $startTime, $callStack['file'] . ':' . $callStack['line'], $uniqueId);

        $response = null;
        try {
            $response = $this->decorated->sendRequest($request);
        } finally {
            $endTime = microtime(true);
            $this->collector->collectTotalTime($response, $endTime, $uniqueId);
            return $response;
        }
    }
}
