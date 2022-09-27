<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Storage;

use League\Flysystem\FilesystemException;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Json\Json;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Dumper;
use Yiisoft\Yii\Filesystem\FilesystemInterface;

use function array_merge;
use function array_slice;
use function count;
use function dirname;
use function filemtime;
use function glob;
use function strlen;
use function substr;
use function uasort;

final class FileStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];

    private int $historySize = 50;

    public function __construct(private string $path, private FilesystemInterface $filesystem, private DebuggerIdGenerator $idGenerator, private Aliases $aliases, private array $excludedClasses = [])
    {
    }

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    public function setHistorySize(int $historySize): void
    {
        $this->historySize = $historySize;
    }

    public function read($type = self::TYPE_INDEX): array
    {
        clearstatcache();
        $data = [];
        $path = $this->aliases->get($this->path);
        $dataFiles = glob($path . '/**/**/' . $type . '.json', GLOB_NOSORT);
        uasort($dataFiles, static fn ($a, $b) => filemtime($a) <=> filemtime($b));

        foreach ($dataFiles as $file) {
            $dir = dirname($file);
            $id = substr($dir, strlen(dirname($file, 2)) + 1);
            $data[$id] = Json::decode(file_get_contents($file));
        }

        return $data;
    }

    public function flush(): void
    {
        $basePath = $this->path . '/' . date('Y-m-d') . '/' . $this->idGenerator->getId() . '/';
        try {
            $dumper = Dumper::create($this->getData(), $this->excludedClasses);
            $jsonData = $dumper->asJson();
            $this->filesystem->write($basePath . self::TYPE_DATA . '.json', $jsonData);

            $jsonObjects = json_decode($dumper->asJsonObjectsMap(), true, 512, JSON_THROW_ON_ERROR);
            $jsonObjects = $this->reindexObjects($jsonObjects);
            $this->filesystem->write($basePath . self::TYPE_OBJECTS . '.json', Dumper::create($jsonObjects)->asJson());

            $indexData = Dumper::create($this->collectIndexData())->asJson();
            $this->filesystem->write($basePath . self::TYPE_INDEX . '.json', $indexData);

            $this->gc();
        } finally {
            $this->collectors = [];
        }
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->collectors as $name => $collector) {
            $data[$name] = $collector->getCollected();
        }

        return $data;
    }

    public function clear(): void
    {
        $this->filesystem->deleteDirectory($this->path);
    }

    /**
     * Collects summary data of current request.
     */
    private function collectIndexData(): array
    {
        $indexData = [
            'id' => $this->idGenerator->getId(),
            'collectors' => array_keys($this->collectors),
        ];

        foreach ($this->collectors as $collector) {
            if ($collector instanceof IndexCollectorInterface) {
                $indexData = array_merge($indexData, $collector->getIndexData());
            }
        }

        return $indexData;
    }

    /**
     * Removes obsolete data files
     *
     * @throws FilesystemException
     */
    private function gc(): void
    {
        $indexFiles = glob($this->aliases->get($this->path) . '/**/**/index.json', GLOB_NOSORT);
        if ((is_countable($indexFiles) ? count($indexFiles) : 0) >= $this->historySize + 1) {
            uasort($indexFiles, static fn ($a, $b) => filemtime($b) <=> filemtime($a));
            $excessFiles = array_slice($indexFiles, $this->historySize);
            foreach ($excessFiles as $file) {
                $path1 = dirname($file);
                $path2 = dirname($file, 2);
                $path3 = dirname($file, 3);
                $resource = substr($path1, strlen($path3));
                $this->filesystem->deleteDirectory($this->path . $resource);

                // Clean empty group directories
                $group = substr($path2, strlen($path3));
                $list = $this->filesystem->listContents($this->path . $group);
                if (empty($list->toArray())) {
                    $this->filesystem->deleteDirectory($this->path . $group);
                }
            }
        }
    }

    private function reindexObjects(array $objectsAsArraysCollection): array
    {
        $result = [];
        foreach ($objectsAsArraysCollection as $objectAsArray) {
            $result = array_merge($result, $objectAsArray);
        }

        return $result;
    }
}
