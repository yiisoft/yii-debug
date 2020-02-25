<?php

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\Yii\Web\Event\AfterMiddleware;
use Yiisoft\Yii\Web\Event\BeforeMiddleware;

final class MiddlewareCollector implements CollectorInterface
{
    use CollectorTrait;

    private array $beforeStack = [];
    private array $afterStack = [];

    public function collected(): array
    {
        return [
            'beforeStack' => $this->beforeStack,
            'afterStack' => $this->afterStack,
        ];
    }

    public function collect(...$payload): void
    {
        $event = current($payload);
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof BeforeMiddleware) {
            $this->beforeStack[] = [
                'name' => get_class($event->getMiddleware()),
                'time' => microtime(true),
                'memory' => memory_get_usage(true),
                'request' => $event->getRequest(),
            ];
        } elseif ($event instanceof AfterMiddleware) {
            $this->afterStack[] = [
                'name' => get_class($event->getMiddleware()),
                'time' => microtime(true),
                'memory' => memory_get_usage(true),
                'response' => $event->getResponse(),
            ];
        }
    }
}
