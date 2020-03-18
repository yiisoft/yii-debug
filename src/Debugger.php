<?php

namespace Yiisoft\Yii\Debug;

use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class Debugger
{
    private string $id;
    /**
     * @var \Yiisoft\Yii\Debug\Collector\CollectorInterface[]
     */
    private array $collectors;
    private StorageInterface $target;

    public function __construct(DebuggerIdGenerator $idGenerator, StorageInterface $target, array $collectors)
    {
        $this->collectors = $collectors;
        $this->target = $target;
        $this->id = $idGenerator->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function startup(): void
    {
        foreach ($this->collectors as $collector) {
            $this->target->addCollector($collector);
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
