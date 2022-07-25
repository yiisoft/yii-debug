<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Queue\Message\MessageInterface;
use Yiisoft\Yii\Queue\QueueInterface;

final class QueueCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $pushes = [];
    private array $statuses = [];
    private array $processingMessages = [];

    public function getCollected(): array
    {
        return [
            'pushes' => $this->pushes,
            'statuses' => $this->statuses,
            'processingMessages' => $this->processingMessages,
        ];
    }

    public function collectStatus(string $id): void
    {
        if (!$this->isActive()) {
            return;
        }

        $this->statuses[] = $id;
    }

    public function collectPush(MessageInterface $message): void
    {
        if (!$this->isActive()) {
            return;
        }
        $this->pushes[] = $message;
    }

    public function collectWorkerProcessing(MessageInterface $message, QueueInterface $queue)
    {
        if (!$this->isActive()) {
            return;
        }
        $this->processingMessages[$queue->getChannelName()][] = $message;
    }

    private function reset(): void
    {
        $this->pushes = [];
        $this->statuses = [];
        $this->processingMessages = [];
    }

    public function getIndexData(): array
    {
        $countPushes = count($this->pushes);
        $countStatuses = count($this->statuses);
        $countProcessingMessages = array_sum(array_map(fn ($messages) => count($messages), $this->processingMessages));

        return [
            'queue' => [
                'countPushes' => $countPushes,
                'countStatuses' => $countStatuses,
                'countProcessingMessages' => $countProcessingMessages,
            ],
        ];
    }
}
