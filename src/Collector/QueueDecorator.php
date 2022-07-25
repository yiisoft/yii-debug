<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Queue\Adapter\AdapterInterface;
use Yiisoft\Yii\Queue\Enum\JobStatus;
use Yiisoft\Yii\Queue\Message\MessageInterface;
use Yiisoft\Yii\Queue\QueueInterface;

final class QueueDecorator implements QueueInterface
{
    public function __construct(
        private QueueInterface $queue,
        private QueueCollector $collector,
    ) {
    }

    public function status(string $id): JobStatus
    {
        $result = $this->queue->status($id);
        $this->collector->collectStatus($id);

        return $result;
    }

    public function push(MessageInterface $message): void
    {
        $this->queue->push($message);
        $this->collector->collectPush($this->queue->getChannelName(), $message);
    }

    public function run(int $max = 0): void
    {
        $this->queue->run($max);
    }

    public function listen(): void
    {
        $this->queue->listen();
    }

    public function withAdapter(AdapterInterface $adapter): QueueInterface
    {
        return $this->queue->withAdapter($adapter);
    }

    public function getChannelName(): string
    {
        return $this->queue->getChannelName();
    }
}
