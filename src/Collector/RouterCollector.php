<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Container\ContainerInterface;
use Yiisoft\Router\UrlMatcherInterface;

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
        $urlMatcher = $this->container->has(UrlMatcherInterface::class) ? $this->container->get(UrlMatcherInterface::class) : null;

        return $urlMatcher === null ? [] :
            [
                'routesTree' => $urlMatcher->getRouteCollection()->getRouteTree(),
                'routes' => $urlMatcher->getRouteCollection()->getRoutes(),
            ];
    }
}
