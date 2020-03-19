<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Filesystem\FilesystemInterface;

final class FileStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    private string $path;

    private DebuggerIdGenerator $idGenerator;

    private FilesystemInterface $filesystem;

    public function __construct(string $path, FilesystemInterface $filesystem, DebuggerIdGenerator $idGenerator)
    {
        $this->path = $path;
        $this->filesystem = $filesystem;
        $this->idGenerator = $idGenerator;
    }

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->collectors as $collector) {
            $data[get_class($collector)] = $collector->getCollected();
        }

        return $data;
    }

    public function flush(): void
    {
        try {
            $varDumper = VarDumper::create($this->getData());
            $jsonData = $varDumper->asJson();
            $this->filesystem->write($this->path . '/' . $this->idGenerator->getId() . '.data.json', $jsonData);

            $jsonObjects = $varDumper->asJsonObjectsMap();
            $this->filesystem->write($this->path . '/' . $this->idGenerator->getId() . '.obj.json', $jsonObjects);
        } finally {
            $this->collectors = [];
        }
    }
}
