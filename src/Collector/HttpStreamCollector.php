<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

final class HttpStreamCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    public function __construct(private array $ignoredPathPatterns = [], private array $ignoredClasses = [])
    {
    }

    private array $requests = [];

    public function getCollected(): array
    {
        return $this->requests;
    }

    public function startup(): void
    {
        $this->isActive = true;
        HttpStreamProxy::register();
        //stream_context_set_default([
        //    'http' => [
        //        'proxy' => 'yii-debug-http://127.0.0.1',
        //    ],
        //]);
        //stream_context_set_default([
        //    'http' => [
        //        'proxy' => 'yii-debug-http://127.0.0.1',
        //    ],
        //]);
        HttpStreamProxy::$collector = $this;
    }

    public function shutdown(): void
    {
        HttpStreamProxy::unregister();
        HttpStreamProxy::$collector = null;

        $this->reset();
        $this->isActive = false;
    }

    public function collect(string $operation, string $path, array $args): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->requests[$operation][] = [
            'uri' => $path,
            'args' => $args,
        ];
    }

    public function getIndexData(): array
    {
        return [
            'http_stream' => array_merge(
                ...array_map(
                    fn (string $operation) => [
                        $operation => is_countable($this->requests[$operation]) ? count(
                            $this->requests[$operation]
                        ) : 0,
                    ],
                    array_keys($this->requests)
                )
            ),
        ];
    }

    private function reset(): void
    {
        $this->requests = [];
    }
}
