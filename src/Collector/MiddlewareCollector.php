<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Web\Event\AfterMiddleware;
use Yiisoft\Yii\Web\Event\BeforeMiddleware;

final class MiddlewareCollector implements CollectorInterface, EventDispatcherInterface
{
    use CollectorTrait;

    private array $beforeStack = [];
    private array $afterStack = [];

    public function collect(): array
    {
        return [
            'beforeStack' => $this->beforeStack,
            'afterStack' => $this->afterStack,
        ];
    }

    public function dispatch(object $event)
    {
        if ($this->isActive()) {
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

        return $event;
    }
}
