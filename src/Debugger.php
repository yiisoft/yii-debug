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
    private DebuggerIdGenerator $idGenerator;

    public function __construct(DebuggerIdGenerator $idGenerator, StorageInterface $target, array $collectors)
    {
        $this->collectors = $collectors;
        $this->target = $target;
        $this->idGenerator = $idGenerator;
    }

    public function getId(): string
    {
        return $this->idGenerator->getId();
    }

    public function startup(): void
    {
        $this->idGenerator->reset();
        foreach ($this->collectors as $collector) {
            $this->target->addCollector($collector);
            $collector->startup();
        }
    }

    public function shutdown(): void
    {
        try {
            $this->target->flush();
        } finally {
            foreach ($this->collectors as $collector) {
                $collector->shutdown();
            }
        }
    }
}
