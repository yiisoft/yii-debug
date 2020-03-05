<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\Container\ContainerInterface;
use Yiisoft\Router\RouteCollectorInterface;

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
        return $this->container->get(RouteCollectorInterface::class)->getItems();
    }
}
