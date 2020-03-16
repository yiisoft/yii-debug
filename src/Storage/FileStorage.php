<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Filesystem\FilesystemInterface;

final class FileStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    private string $path;

    private ?string $debugId = null;

    private FilesystemInterface $filesystem;

    public function __construct(string $path, FilesystemInterface $filesystem)
    {
        $this->path = $path;
        $this->filesystem = $filesystem;
    }

    public function setDebugId(string $id): void
    {
        if ($this->debugId === null) {
            $this->debugId = $id;
        }
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
        $jsonData = VarDumper::dumpAsJson($this->getData());
        $this->filesystem->write($this->path . '/' . $this->debugId . '.data.json', $jsonData);

        $jsonObjects = VarDumper::dumpCurrentObjectsCacheAsJson();
        $this->filesystem->write($this->path . '/' . $this->debugId . '.obj.json', $jsonObjects);

        $this->collectors = [];
    }
}
