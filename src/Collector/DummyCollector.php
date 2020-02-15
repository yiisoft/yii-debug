<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Debug\Target\TargetInterface;

class DummyCollector implements CollectorInterface
{
    private TargetInterface $target;

    public function __construct(TargetInterface $target)
    {
        $this->target = $target;
    }

    public function export(): void
    {
        $this->target->add($this->getData());
    }

    public function getData()
    {
        return [
            'int' => 123,
            'str' => 'asdas',
            'object' => new \stdClass(),
        ];
    }
}
