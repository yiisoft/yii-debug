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
    private StorageInterface $storage;

    public function __construct(string $id, StorageInterface $storage, array $collectors)
    {
        $this->collectors = $collectors;
        $this->storage = $storage;
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function startup(): void
    {
        foreach ($this->collectors as $collector) {
            $this->storage->addCollector($collector);
            $collector->startup();
        }
    }

    public function shutdown(): void
    {
        foreach ($this->collectors as $collector) {
            $collector->shutdown();
        }
        $this->storage->flush();
    }
}
