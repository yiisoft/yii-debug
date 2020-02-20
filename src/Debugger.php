<?php

namespace Yiisoft\Yii\Debug;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;

class Debugger
{
    /**
     * @var \Yiisoft\Yii\Debug\Collector\CollectorInterface[]
     */
    private array $collectors;

    public function __construct(CollectorInterface ...$collectors)
    {
        $this->collectors = $collectors;
    }

    public function startup(): void
    {
        foreach ($this->collectors as $collector) {
            $collector->startup();
        }
    }

    public function shutdown(): void
    {
        foreach ($this->collectors as $collector) {
            $collector->shutdown();
            $collector->export();
        }
    }
}
