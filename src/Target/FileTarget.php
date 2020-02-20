<?php

namespace Yiisoft\Yii\Debug\Target;

use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class FileTarget implements TargetInterface
{
    private array $collectors = [];
    private array $data = [];
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function persist(CollectorInterface $collector): void
    {
        $this->collectors[get_class($collector)] = $collector;
    }

    public function getData(): array
    {
        foreach ($this->collectors as $collector) {
            $this->data[get_class($collector)] = $collector->collect();
        }

        return $this->data;
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
    }
}
