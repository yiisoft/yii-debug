<?php

namespace Yiisoft\Yii\Debug;

use Yiisoft\Yii\Debug\Target\TargetInterface;

class Debugger
{
    private string $id;
    /**
     * @var \Yiisoft\Yii\Debug\Collector\CollectorInterface[]
     */
    private array $collectors;
    private TargetInterface $target;

    public function __construct(TargetInterface $target, array $collectors)
    {
        $this->collectors = $collectors;
        $this->target = $target;
        $this->id = uniqid('yii-debug-', true);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function startup(): void
    {
        foreach ($this->collectors as $collector) {
            $this->target->persist($collector);
            $collector->startup();
        }
    }

    public function shutdown(): void
    {
        foreach ($this->collectors as $collector) {
            $collector->shutdown();
        }
        $this->target->flush();
    }
}
