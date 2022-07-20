<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

class LogCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $messages = [];

    public function getCollected(): array
    {
        return $this->messages;
    }

    public function collect(string $level, $message, array $context, string $line): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->messages[] = [
            'time' => microtime(true),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'line' => $line,
        ];
    }

    private function reset(): void
    {
        $this->messages = [];
    }

    public function getIndexData(): array
    {
        return [
            'logger' => [
                'total' => count($this->messages),
            ],
        ];
    }
}
