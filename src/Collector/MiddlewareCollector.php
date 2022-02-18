<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Middleware\Dispatcher\Event\AfterMiddleware;
use Yiisoft\Middleware\Dispatcher\Event\BeforeMiddleware;

final class MiddlewareCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $beforeStack = [];
    private array $actionHandler = [];
    private array $afterStack = [];

    #[ArrayShape(['beforeStack' => 'array', 'actionHandler' => 'array', 'afterStack' => 'array'])]
    public function getCollected(): array
    {
        array_pop($this->beforeStack);
        array_shift($this->afterStack);
        return [
            'beforeStack' => $this->beforeStack,
            'actionHandler' => $this->actionHandler,
            'afterStack' => $this->afterStack,
        ];
    }

    public function collect(BeforeMiddleware|AfterMiddleware ...$payload): void
    {
        $event = current($payload);
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if (
            method_exists($event->getMiddleware(), '__debugInfo')
            && (new \ReflectionClass($event->getMiddleware()))->isAnonymous()
        ) {
            $name = implode('::', $event->getMiddleware()->__debugInfo()['callback']);
        } else {
            $name = get_class($event->getMiddleware());
        }
        if ($event instanceof BeforeMiddleware) {
            $this->beforeStack[] = $this->actionHandler = [
                'name' => $name,
                'time' => microtime(true),
                'memory' => memory_get_usage(),
                'request' => $event->getRequest(),
            ];
        } elseif ($event instanceof AfterMiddleware) {
            $this->afterStack[] = [
                'name' => $name,
                'time' => microtime(true),
                'memory' => memory_get_usage(),
                'response' => $event->getResponse(),
            ];
        }
    }

    private function reset(): void
    {
        $this->beforeStack = [];
        $this->actionHandler = [];
        $this->afterStack = [];
    }

    #[ArrayShape(['totalMiddlewares' => 'int'])]
    public function getIndexData(): array
    {
        return [
            'totalMiddlewares' => count($this->beforeStack),
        ];
    }
}
