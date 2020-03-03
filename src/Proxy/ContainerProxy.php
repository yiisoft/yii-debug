<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Di\Container;
use Yiisoft\Yii\Debug\Collector\CommonServiceCollectorInterface;

final class ContainerProxy extends ContainerInterfaceProxy
{
    public function __construct(
        ContainerInterface $container,
        ContainerProxyConfig $config
    ) {
        $container = $container instanceof Container ? $container->withParentContainer($this) : $container;
        parent::__construct($container, $config);
    }

    public function set(string $id, $definition): void
    {
        $this->checkNativeContainer();
        $this->resetCurrentError();
        try {
            $timeStart = microtime(true);
            $this->container->set($id, $definition);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->log('set', [$id, $definition], null, $timeStart);
        }
    }

    public function setMultiple(array $config): void
    {
        $this->checkNativeContainer();
        $this->resetCurrentError();
        try {
            $timeStart = microtime(true);
            $this->container->setMultiple($config);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->log('setMultiple', [$config], null, $timeStart);
        }
    }

    public function addProvider($providerDefinition): void
    {
        $this->checkNativeContainer();
        $this->resetCurrentError();
        try {
            $timeStart = microtime(true);
            $this->container->addProvider($providerDefinition);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->log('addProvider', [$providerDefinition], null, $timeStart);
        }
    }

    public function withParentContainer(ContainerInterface $container): ContainerInterface
    {
        $this->checkNativeContainer();
        $this->container = $this->container->withParentContainer($container);

        return $this;
    }

    private function checkNativeContainer(): void
    {
        if (!$this->container instanceof Container) {
            throw new \RuntimeException('This method is for Yiisoft\Di\Container only');
        }
    }
}
