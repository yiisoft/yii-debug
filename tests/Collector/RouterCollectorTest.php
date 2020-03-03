<?php

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Yiisoft\Di\Container;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\RouterCollector;

final class RouterCollectorTest extends CollectorTestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Yiisoft\Router\RouteCollectorInterface
     */
    private $routeCollector;

    private array $routes = [];

    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\RouterCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $this->routes = $this->createRoutes();
        $this->routeCollector
            ->method('getItems')
            ->willReturn($this->routes);
    }

    protected function getCollector(): CollectorInterface
    {
        $this->routeCollector = $this->createMock(RouteCollectorInterface::class);
        $container = new Container([
            RouteCollectorInterface::class => $this->routeCollector,
        ]);

        return new RouterCollector($container);
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);
        $this->assertSame($collector->getCollected(), $this->routes);
    }

    private function createRoutes(): array
    {
        return [
            Route::get('/'),
            Group::create('/'),
        ];
    }
}
