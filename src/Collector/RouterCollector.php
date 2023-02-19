<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Container\ContainerInterface;
use ReflectionObject;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollectionInterface;

class RouterCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private float $matchTime = 0;

    public function __construct(private ContainerInterface $container)
    {
    }

    public function collect(float $matchTime): void
    {
        if (!$this->isActive()) {
            return;
        }
        $this->matchTime = $matchTime;
    }

    public function getCollected(): array
    {
        $routeCollection = $this->container->has(RouteCollectionInterface::class)
            ? $this->container->get(RouteCollectionInterface::class)
            : null;

        $currentRoute = $this->getCurrentRoute();
        $route = $this->getRouteByCurrentRoute($currentRoute);
        [$middlewares, $action] = $this->getMiddlewaresAndAction($route);

        $result = [
            'currentRoute' => null,
        ];
        if ($currentRoute !== null && $route !== null) {
            $result['currentRoute'] = [
                'matchTime' => $this->matchTime,
                'name' => $route->getData('name'),
                'pattern' => $route->getData('pattern'),
                'arguments' => $currentRoute->getArguments(),
                'host' => $route->getData('host'),
                'uri' => (string) $currentRoute->getUri(),
                'action' => $action,
                'middlewares' => $middlewares,
            ];
        }
        if ($routeCollection !== null) {
            $result['routesTree'] = $routeCollection->getRouteTree();
            $result['routes'] = $routeCollection->getRoutes();
            $result['routeTime'] = $this->matchTime;
        }
        return $result;
    }

    public function getIndexData(): array
    {
        $currentRoute = $this->getCurrentRoute();
        $route = $this->getRouteByCurrentRoute($currentRoute);

        if ($currentRoute === null || $route === null) {
            return [
                'router' => null,
            ];
        }

        [$middlewares, $action] = $this->getMiddlewaresAndAction($route);

        return [
            'router' => [
                'matchTime' => $this->matchTime,
                'name' => $route->getData('name'),
                'pattern' => $route->getData('pattern'),
                'arguments' => $currentRoute->getArguments(),
                'host' => $route->getData('host'),
                'uri' => (string) $currentRoute->getUri(),
                'action' => $action,
                'middlewares' => $middlewares,
            ],
        ];
    }

    private function getCurrentRoute(): ?CurrentRoute
    {
        return $this->container->has(CurrentRoute::class) ? $this->container->get(CurrentRoute::class) : null;
    }

    private function getRouteByCurrentRoute(?CurrentRoute $currentRoute): ?Route
    {
        if ($currentRoute === null) {
            return null;
        }
        $reflection = new ReflectionObject($currentRoute);

        $reflectionProperty = $reflection->getProperty('route');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($currentRoute);
    }

    private function getMiddlewaresAndAction(?Route $route): array
    {
        if ($route === null) {
            return [[], null];
        }
        $reflection = new ReflectionObject($route);
        $reflectionProperty = $reflection->getProperty('middlewareDefinitions');
        $reflectionProperty->setAccessible(true);
        $middlewareDefinitions = $reflectionProperty->getValue($route);
        $action = array_pop($middlewareDefinitions);
        return [$middlewareDefinitions, $action];
    }
}
