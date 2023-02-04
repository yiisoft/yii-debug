<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

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
                'count' => array_sum(array_map(count(...), $this->requests)),
                'totalTime' => array_sum(
                    array_merge(
                        ...array_map(
                            fn (array $entry) => array_column($entry, 'totalTime'),
                            $this->requests
                        )
                    )
                ),
            ],
        ];
    }

    public function collect(\Psr\Http\Message\RequestInterface $request, string $line)
    {
        if (!$this->isActive()) {
            return;
        }

        $this->requests[spl_object_id($request)][] = [
            'startTime' => microtime(true),
            'endTime' => microtime(true),
            'totalTime' => 0,
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->__toString(),
            'headers' => $request->getHeaders(),
            'line' => $line,
        ];
    }

    public function collectTotalTime(\Psr\Http\Message\RequestInterface $request)
    {
        if (!$this->isActive()) {
            return;
        }

        if (!isset($this->requests[spl_object_id($request)])) {
            return;
        }
        $entry = &$this->requests[spl_object_id($request)][count($this->requests[spl_object_id($request)])-1];
        $entry['endTime'] = microtime(true);
        $entry['totalTime'] = $entry['endTime'] - $entry['startTime'];
    }
}
