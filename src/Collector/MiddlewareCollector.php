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
    private array $afterStack = [];

    #[ArrayShape(['beforeStack' => 'array', 'actionHandler' => 'array', 'afterStack' => 'array'])]
    public function getCollected(): array
    {
        $beforeStack = $this->beforeStack;
        $afterStack = $this->afterStack;
        $beforeAction = array_pop($beforeStack);
        $afterAction = array_shift($afterStack);
        $actionHandler = [];

        if ($beforeAction !== null && $afterAction !== null) {
            $actionHandler = $this->getActionHandler($beforeAction, $afterAction);
        }

        return [
            'beforeStack' => $beforeStack,
            'actionHandler' => $actionHandler,
            'afterStack' => $afterStack,
        ];
    }

    public function collect(BeforeMiddleware|AfterMiddleware $event): void
    {
        if (!$this->isActive()) {
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
            $this->beforeStack[] = [
                'name' => $name,
                'time' => microtime(true),
                'memory' => memory_get_usage(),
                'request' => $event->getRequest(),
            ];
        } else {
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
        $this->afterStack = [];
    }

    #[ArrayShape(['totalMiddlewares' => 'int'])]
    public function getIndexData(): array
    {
        return [
            'totalMiddlewares' => ($total = count($this->beforeStack)) > 0 ? $total - 1 : 0, // Remove action handler
        ];
    }

    #[ArrayShape([
        'name' => 'string',
        'startTime' => 'float',
        'request' => 'object',
        'response' => 'object',
        'endTime' => 'float',
        'memory' => 'int',
    ])]
    private function getActionHandler(array $beforeAction, array $afterAction): array
    {
        return [
            'name' => $beforeAction['name'],
            'startTime' => $beforeAction['time'],
            'request' => $beforeAction['request'],
            'response' => $afterAction['response'],
            'endTime' => $afterAction['time'],
            'memory' => $afterAction['memory'],
        ];
    }
}
