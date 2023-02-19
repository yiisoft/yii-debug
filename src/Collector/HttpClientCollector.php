<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function count;

final class HttpClientCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $requests = [];

    public function getCollected(): array
    {
        return array_merge(...array_values($this->requests));
    }

    public function getIndexData(): array
    {
        return [
            'http' => [
                'count' => array_sum(array_map(static fn ($requests) => count($requests), $this->requests)),
                'totalTime' => array_sum(
                    array_merge(
                        ...array_map(
                            static fn (array $entry) => array_column($entry, 'totalTime'),
                            $this->requests
                        )
                    )
                ),
            ],
        ];
    }

    public function collect(RequestInterface $request, float|string $startTime, string $line, ?string $uniqueId)
    {
        if (!$this->isActive()) {
            return;
        }

        $this->requests[$uniqueId][] = [
            'startTime' => $startTime,
            'endTime' => $startTime,
            'totalTime' => 0,
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->__toString(),
            'headers' => $request->getHeaders(),
            'line' => $line,
            'response' => null,
        ];
    }

    public function collectTotalTime(?ResponseInterface $response, float|string $startTime, ?string $uniqueId): void
    {
        if (!$this->isActive()) {
            return;
        }

        if (!isset($this->requests[$uniqueId]) || !is_array($this->requests[$uniqueId])) {
            return;
        }
        $entry = &$this->requests[$uniqueId][count($this->requests[$uniqueId]) - 1];
        if ($response instanceof ResponseInterface) {
            $entry['responseRaw'] = Message::toString($response);
            $entry['responseStatus'] = $response->getStatusCode();
            Message::rewindBody($response);
        }
        $entry['endTime'] = $startTime;
        $entry['totalTime'] = $entry['endTime'] - $entry['startTime'];
    }
}
