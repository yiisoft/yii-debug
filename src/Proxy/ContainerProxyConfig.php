<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\CommonServiceCollectorInterface;

class ContainerProxyConfig
{
    private int $logLevel = 0;

    private array $decoratedServices = [];

    private bool $active = false;

    private ?EventDispatcherInterface $dispatcher = null;

    private ?CommonServiceCollectorInterface $commonCollector = null;

    private ?string $proxyCachePath = null;

    public function __construct(
        bool $active,
        array $decoratedServices,
        EventDispatcherInterface $dispatcher = null,
        CommonServiceCollectorInterface $commonCollector = null,
        string $proxyCachePath = null,
        int $logLevel = 0
    )
    {
        $this->active = $active;
        $this->decoratedServices = $decoratedServices;
        $this->dispatcher = $dispatcher;
        $this->commonCollector = $commonCollector;
        $this->proxyCachePath = $proxyCachePath;
        $this->logLevel = $logLevel;
    }

    public function getIsActive(): bool
    {
        return $this->active;
    }

    public function getLogLevel(): int
    {
        return $this->logLevel;
    }

    public function getDecoratedServices(): array
    {
        return $this->decoratedServices;
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function getCollector(): CommonServiceCollectorInterface
    {
        return $this->commonCollector;
    }

    public function getProxyCachePath(): string
    {
        return $this->proxyCachePath;
    }
}