<?php

namespace Yiisoft\Yii\Debug\Collector;

final class LogCollector implements LogCollectorInterface
{
    use CollectorTrait;

    private array $messages = [];

    public function getCollected(): array
    {
        return $this->messages;
    }

    public function collect(string $level, string $message, array $context): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->messages[] = [
            'time' => microtime(true),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }

    private function reset(): void
    {
        $this->messages = [];
    }
}
