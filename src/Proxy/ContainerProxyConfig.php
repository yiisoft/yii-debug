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

    private ?CommonServiceCollectorInterface $collector = null;

    private ?string $proxyCachePath = null;

    public function __construct(
        bool $active = false,
        array $decoratedServices = [],
        EventDispatcherInterface $dispatcher = null,
        CommonServiceCollectorInterface $collector = null,
        string $proxyCachePath = null,
        int $logLevel = 0
    ) {
        $this->active = $active;
        $this->decoratedServices = $decoratedServices;
        $this->dispatcher = $dispatcher;
        $this->collector = $collector;
        $this->proxyCachePath = $proxyCachePath;
        $this->logLevel = $logLevel;
    }

    public function activate(): self
    {
        $config = clone $this;
        $config->active = true;
        return $config;
    }

    public function withDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $config = clone $this;
        $config->dispatcher = $dispatcher;
        return $config;
    }

    public function withLogLevel(int $logLevel): self
    {
        $config = clone $this;
        $config->logLevel = $logLevel;
        return $config;
    }

    public function withProxyCachePath(int $proxyCachePath): self
    {
        $config = clone $this;
        $config->proxyCachePath = $proxyCachePath;
        return $config;
    }

    public function withCollector(CommonServiceCollectorInterface $collector): self
    {
        $config = clone $this;
        $config->collector = $collector;
        return $config;
    }

    public function withDecoratedServices(array $decoratedServices): self
    {
        $config = clone $this;
        $config->decoratedServices = $decoratedServices;
        return $config;
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

    public function getDispatcher(): ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function getCollector(): ?CommonServiceCollectorInterface
    {
        return $this->collector;
    }

    public function getProxyCachePath(): ?string
    {
        return $this->proxyCachePath;
    }
}
