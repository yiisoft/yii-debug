<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;

final class LogCollector implements LogCollectorInterface, IndexCollectorInterface
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
            'line' => $line
        ];
    }

    private function reset(): void
    {
        $this->messages = [];
    }

    #[ArrayShape(['totalLogs' => 'int'])]
    public function getIndexData(): array
    {
        return [
            'totalLogs' => count($this->messages),
        ];
    }
}
