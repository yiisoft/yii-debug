<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

final class FilesystemStreamCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    public function __construct(private array $ignoredPathPatterns = [], private array $ignoredClasses = [])
    {
    }

    /**
     * @var array[]
     */
    private array $operations = [];

    public function getCollected(): array
    {
        return array_map('array_values', $this->operations);
    }

    public function startup(): void
    {
        $this->isActive = true;
        FilesystemStreamProxy::register();
        FilesystemStreamProxy::$collector = $this;
        FilesystemStreamProxy::$ignoredPathPatterns = $this->ignoredPathPatterns;
        FilesystemStreamProxy::$ignoredClasses = $this->ignoredClasses;
    }

    public function shutdown(): void
    {
        FilesystemStreamProxy::unregister();
        FilesystemStreamProxy::$collector = null;
        FilesystemStreamProxy::$ignoredPathPatterns = [];
        FilesystemStreamProxy::$ignoredClasses = [];

        $this->reset();
        $this->isActive = false;
    }

    public function collect(string $operation, string $path, array $args): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->operations[$operation][] = [
            'path' => $path,
            'args' => $args,
        ];
    }

    public function getIndexData(): array
    {
        return [
            'fs_stream' => array_merge(
                ...array_map(
                    fn (string $operation) => [$operation => count($this->operations[$operation])],
                    array_keys($this->operations)
                )
            ),
        ];
    }

    private function reset(): void
    {
        $this->operations = [];
    }
}
