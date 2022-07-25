<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use InvalidArgumentException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\Queue\Adapter\AdapterInterface;
use Yiisoft\Yii\Queue\Enum\JobStatus;
use Yiisoft\Yii\Queue\Exception\BehaviorNotSupportedException;
use Yiisoft\Yii\Queue\Message\MessageInterface;
use Yiisoft\Yii\Queue\QueueInterface;
use Yiisoft\Yii\Queue\Worker\WorkerInterface;

final class QueueWorkerInterfaceProxy implements WorkerInterface
{
    public function __construct(
        private WorkerInterface $worker,
        private QueueCollector $collector,
    ) {
    }

    public function process(MessageInterface $message, QueueInterface $queue): void
    {
        $this->collector->collectWorkerProcessing($message, $queue);
        $this->worker->process($message, $queue);
    }
}
