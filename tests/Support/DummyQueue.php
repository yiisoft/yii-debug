<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Support;

use Exception;
use Yiisoft\Yii\Queue\Adapter\AdapterInterface;
use Yiisoft\Yii\Queue\Enum\JobStatus;
use Yiisoft\Yii\Queue\Message\MessageInterface;
use Yiisoft\Yii\Queue\QueueInterface;

final class DummyQueue implements QueueInterface
{
    public function __construct(private $channelName)
    {
    }

    public function push(MessageInterface $message): void
    {
    }

    public function run(int $max = 0): void
    {
    }

    public function listen(): void
    {
    }

    public function status(string $id): JobStatus
    {
        throw new Exception('`status()` method is not implemented yet.');
    }

    public function withAdapter(AdapterInterface $adapter): QueueInterface
    {
        throw new Exception('`withAdapter()` method is not implemented yet.');
    }

    public function getChannelName(): string
    {
        return $this->channelName;
    }
}
