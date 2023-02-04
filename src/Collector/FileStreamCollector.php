<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

final class FileStreamCollector implements CollectorInterface, IndexCollectorInterface
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
        FileStreamProxy::register();
        FileStreamProxy::$collector = $this;
        FileStreamProxy::$ignoredPathPatterns = $this->ignoredPathPatterns;
        FileStreamProxy::$ignoredClasses = $this->ignoredClasses;
    }

    public function shutdown(): void
    {
        FileStreamProxy::unregister();
        FileStreamProxy::$collector = null;
        FileStreamProxy::$ignoredPathPatterns = [];
        FileStreamProxy::$ignoredClasses = [];

        $this->reset();
        $this->isActive = false;
    }

    public function collect(string $operation, string $path, array $args): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->requests[$operation][] = [
            'path' => $path,
            'args' => $args,
        ];
    }

    public function getIndexData(): array
    {
        return [
            'file' => array_merge(
                ...array_map(
                    fn (string $operation) => [$operation => count($this->requests[$operation])],
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
