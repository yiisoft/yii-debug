<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\Aliases\Aliases;
use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Filesystem\FilesystemInterface;

final class FileStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    private string $path;

    private int $historySize = 50;

    private DebuggerIdGenerator $idGenerator;

    private FilesystemInterface $filesystem;

    private Aliases $aliases;

    public function __construct(string $path, FilesystemInterface $filesystem, DebuggerIdGenerator $idGenerator, Aliases $aliases)
    {
        $this->path = $path;
        $this->filesystem = $filesystem;
        $this->idGenerator = $idGenerator;
        $this->aliases = $aliases;
    }

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function setHistorySize(int $historySize): void
    {
        $this->historySize = $historySize;
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

            $indexData = VarDumper::create($this->collectIndex())->asJson();
            $this->filesystem->write($this->path . '/' . $this->idGenerator->getId() . '.index.json', $indexData);

            $this->gc();
        } finally {
            $this->collectors = [];
        }
    }

    /**
     * Collects summary data of current request.
     * @return array
     */
    private function collectIndex(): array
    {
        $indexed = ['tag' => $this->idGenerator->getId()];

        foreach ($this->collectors as $collector) {
            if ($collector instanceof IndexCollectorInterface) {
                $indexed = \array_merge($indexed, $collector->getIndexed());
            }
        }

        return $indexed;
    }

    /**
     * Removes obsolete data files
     * @throws \League\Flysystem\FilesystemException
     */
    private function gc(): void
    {
        $indexFiles = \glob($this->aliases->get($this->path) . '/yii-debug*.index.json', GLOB_NOSORT);
        if (\count($indexFiles) >= $this->historySize + 1) {
            \uasort($indexFiles, fn($a, $b) => @\filemtime($b) <=> @\filemtime($a));
            $excessFiles = \array_slice($indexFiles, $this->historySize);
            foreach ($excessFiles as $file) {
                $tag = \basename($file, '.index.json');
                $indexFile = $this->path . "/$tag.index.json";
                $dataFile = $this->path . "/$tag.data.json";
                $objFile = $this->path . "/$tag.obj.json";
                $this->filesystem->delete($indexFile);
                $this->filesystem->delete($dataFile);
                $this->filesystem->delete($objFile);
            }
        }
    }
}
