<?php

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
        return $this->container->has(UrlMatcherInterface::class) ?
            $this->container->get(UrlMatcherInterface::class)->getRouteCollection()->getRouteTree() : [];
    }
}
