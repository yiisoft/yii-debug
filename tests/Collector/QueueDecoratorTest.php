<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\Queue\QueueCollector;
use Yiisoft\Yii\Debug\Collector\Queue\QueueDecorator;
use Yiisoft\Yii\Queue\Adapter\AdapterInterface;
use Yiisoft\Yii\Queue\QueueInterface;

class QueueDecoratorTest extends TestCase
{
    public function testWithAdapter()
    {
        $queue = $this->createMock(QueueInterface::class);
        $collector = new QueueCollector();
        $decorator = new QueueDecorator(
            $queue,
            $collector,
        );

        $queueAdapter = $this->createMock(AdapterInterface::class);

        $newDecorator = $decorator->withAdapter($queueAdapter);

        $this->assertInstanceOf(QueueDecorator::class, $newDecorator);
    }
}
