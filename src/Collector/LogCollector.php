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

    public function collect(...$payload): void
    {
        if (count($payload) !== 3) {
            throw new \InvalidArgumentException('$payload should contain $level, $message and $context variables');
        }
        [$level, $message, $context] = $payload;

        $this->messages[] = [
            'time' => microtime(true),
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }
}
