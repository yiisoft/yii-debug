<?php

namespace Yiisoft\Yii\Debug\Collector;

class DummyCollector implements CollectorInterface
{
    use CollectorTrait;

    public function collect(): array
    {
        return $this->getData();
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
