<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Container\ContainerInterface;
use Yiisoft\Router\RouteCollectionInterface;

final class RouterCollector implements CollectorInterface
{
    use CollectorTrait;

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
            ];
    }
}
