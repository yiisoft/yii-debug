<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Container\ContainerInterface;
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

    public function getIndexData(): array
    {
        return [
            'routeMatchTime' => $this->matchTime,
        ];
    }
}
