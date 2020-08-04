<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Debug\Collector\WebAppInfoCollector;
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

            $this->updateIndex();
        } finally {
            $this->collectors = [];
        }
    }

    /**
     * Updates index file with summary log data
     * @throws \JsonException
     * @throws \League\Flysystem\FilesystemException
     */
    private function updateIndex(): void
    {
        $summary = $this->collectSummary();
        $indexFile = $this->path . '/index.json';
        if (!$this->filesystem->fileExists($indexFile)) {
            $this->filesystem->write($indexFile, '');
        } elseif (($manifest = $this->filesystem->read($indexFile)) === false) {
            throw new \RuntimeException("Unable to open debug data index file: $indexFile");
        }

        if (empty($manifest)) {
            // error while reading index data, ignore and create new
            $manifest = [];
        } else {
            $manifest = json_decode($manifest, true, 512, JSON_THROW_ON_ERROR);
        }

        $manifest[$this->idGenerator->getId()] = $summary;

        $this->gc($manifest);
        $this->filesystem->write($indexFile, VarDumper::create($manifest)->asJson());
    }

    /**
     * Collects summary data of current request.
     * @return array
     */
    private function collectSummary(): array
    {
        if (
            !array_key_exists(RequestCollector::class, $this->getData())
            || !array_key_exists(WebAppInfoCollector::class, $this->getData())
        ) {
            return [];
        }

        $requestData = $this->getData()[RequestCollector::class];
        $appInfoData = $this->getData()[WebAppInfoCollector::class];

        return [
            'tag' => $this->idGenerator->getId(),
            'url' => $requestData['request_url'],
            'ajax' => (int)$requestData['request_is_ajax'],
            'method' => $requestData['request_method'],
            'ip' => $requestData['user_ip'],
            'time' => $appInfoData['request_processing_time'],
            'statusCode' => $requestData['response_status_code'],
        ];
    }

    /**
     * Removes obsolete data files
     * @param array $manifest
     * @throws \League\Flysystem\FilesystemException
     */
    private function gc(array &$manifest): void
    {
        if (count($manifest) > $this->historySize + 1) {
            $n = count($manifest) - $this->historySize;
            foreach (array_keys($manifest) as $tag) {
                $dataFile = $this->path . "/$tag.data.json";
                $objFile = $this->path . "/$tag.obj.json";
                $this->filesystem->delete($dataFile);
                $this->filesystem->delete($objFile);
                unset($manifest[$tag]);
                if (--$n <= 0) {
                    break;
                }
            }
        }
    }
}
