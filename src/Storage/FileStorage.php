<?php

namespace Yiisoft\Yii\Debug\Storage;

use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class FileStorage implements StorageInterface
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors = [];
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function addCollector(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->collectors as $collector) {
            $data[get_class($collector)] = $collector->collected();
        }

        return $data;
    }

    public function flush(): void
    {
        $content = VarDumper::dumpAsString($this->getData());
        if (file_exists($this->path)) {
            $result = file_put_contents($this->path, $content, FILE_APPEND);
        } else {
            $result = file_put_contents($this->path, $content);
        }
        if (!$result) {
            throw new \RuntimeException('error ' . (int)$result);
        }
        $this->collectors = [];
    }
}
