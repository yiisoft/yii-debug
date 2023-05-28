<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Web;

use ReflectionClass;
use Yiisoft\Middleware\Dispatcher\Event\AfterMiddleware;
use Yiisoft\Middleware\Dispatcher\Event\BeforeMiddleware;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;

final class MiddlewareCollector implements SummaryCollectorInterface
{
    use CollectorTrait;

    private array $beforeStack = [];
    private array $afterStack = [];

    public function getCollected(): array
    {
        if (!$this->isActive()) {
            return [];
        }
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
            && (new ReflectionClass($event->getMiddleware()))->isAnonymous()
        ) {
            $callback = $event->getMiddleware()->__debugInfo()['callback'];
            if (is_array($callback)) {
                $name = implode('::', $callback);
            } else {
                $name = 'object(Closure)#' . spl_object_id($callback);
            }
        } else {
            $name = $event->getMiddleware()::class;
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

    public function getSummary(): array
    {
        if (!$this->isActive()) {
            return [];
        }
        return [
            'middleware' => [
                'total' => ($total = count($this->beforeStack)) > 0 ? $total - 1 : 0, // Remove action handler
            ],
        ];
    }

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
