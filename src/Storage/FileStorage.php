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

            $this->updateManifest();
        } finally {
            $this->collectors = [];
        }
    }

    private function updateManifest(): void
    {
        $summary = $this->collectSummary();
        $indexFile = $this->path . '/index.data';
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

        $this->filesystem->write($indexFile, VarDumper::create($manifest)->asJson());
    }

    public function collectSummary(): array
    {
        if (
            !array_key_exists(RequestCollector::class, $this->getData())
            || !array_key_exists(WebAppInfoCollector::class, $this->getData())
        ) {
            return [];
        }

        $data = $this->getData()[RequestCollector::class];
        $appInfoData = $this->getData()[WebAppInfoCollector::class];

        return [
            'tag' => $this->idGenerator->getId(),
            'url' => $data['request_url'],
            'ajax' => (int)$data['request_is_ajax'],
            'method' => $data['request_method'],
            'ip' => $data['user_ip'],
            'time' => $appInfoData['request_processing_time'],
            'statusCode' => $data['response_status_code'],
        ];
    }
}
