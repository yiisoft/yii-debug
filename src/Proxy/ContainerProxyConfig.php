<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Yii\Debug\Collector\ServiceCollectorInterface;

final class ContainerProxyConfig
{
    private int $logLevel;

    private array $decoratedServices;

    private bool $active;

    private ?EventDispatcherInterface $dispatcher;

    private ?ServiceCollectorInterface $collector;

    private ?string $proxyCachePath;

    public function __construct(
        bool $active = false,
        array $decoratedServices = [],
        EventDispatcherInterface $dispatcher = null,
        ServiceCollectorInterface $collector = null,
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

    public function withCollector(ServiceCollectorInterface $collector): self
    {
        $config = clone $this;
        $config->collector = $collector;

        return $config;
    }

    public function withDecoratedServices(array $decoratedServices): self
    {
        $config = clone $this;
        $config->decoratedServices = array_merge($this->decoratedServices, $decoratedServices);

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

    public function getCollector(): ?ServiceCollectorInterface
    {
        return $this->collector;
    }

    public function getProxyCachePath(): ?string
    {
        return $this->proxyCachePath;
    }

    public function getDecoratedServiceConfig($service)
    {
        return $this->decoratedServices[$service];
    }

    public function hasDecoratedService(string $service): bool
    {
        return isset($this->decoratedServices[$service]) || in_array($service, $this->decoratedServices, true);
    }

    public function hasDecoratedServiceArrayConfig(string $service): bool
    {
        return isset($this->decoratedServices[$service]) && is_array($this->decoratedServices[$service]);
    }

    public function hasDecoratedServiceArrayConfigWithStringKeys(string $service): bool
    {
        return $this->hasDecoratedServiceArrayConfig($service) && !isset($this->decoratedServices[$service][0]);
    }

    public function hasDecoratedServiceCallableConfig(string $service): bool
    {
        return isset($this->decoratedServices[$service]) && is_callable($this->decoratedServices[$service]);
    }

    public function hasDispatcher(): bool
    {
        return $this->dispatcher !== null;
    }

    public function hasCollector(): bool
    {
        return $this->collector !== null;
    }
}
