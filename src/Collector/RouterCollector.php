<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use JetBrains\PhpStorm\ArrayShape;
use Psr\Container\ContainerInterface;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\RouteCollectionInterface;

final class RouterCollector implements RouterCollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private ContainerInterface $container;
    private float $matchTime = 0;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

        return $routeCollection === null ? [] :
            [
                'routesTree' => $routeCollection->getRouteTree(),
                'routes' => $routeCollection->getRoutes(),
                'routeTime' => $this->matchTime,
            ];
    }

    #[ArrayShape(['routeMatchTime' => 'float|int', 'matchedRoute' => 'mixed'])]
    public function getIndexData(): array
    {
        $currentRoute = $this->container->has(CurrentRoute::class) ? $this->container->get(CurrentRoute::class) : null;
        return [
            'routeMatchTime' => $this->matchTime,
            'matchedRoute' => $currentRoute?->getName(),
        ];
    }
}
