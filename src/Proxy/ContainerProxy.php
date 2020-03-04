<?php

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerDelegateInterface;

final class ContainerProxy extends ContainerInterfaceProxy implements ContainerDelegateInterface
{
    public function __construct(
        ContainerInterface $container,
        ContainerProxyConfig $config
    ) {
        $container = $container instanceof ContainerDelegateInterface ? $container->delegateLookup($this) : $container;
        parent::__construct($container, $config);
    }

    public function set(string $id, $definition): void
    {
        $this->checkNativeContainer();
        $this->resetCurrentError();
        $timeStart = microtime(true);
        try {
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
        $timeStart = microtime(true);
        try {
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
        $timeStart = microtime(true);
        try {
            $this->container->addProvider($providerDefinition);
        } catch (ContainerExceptionInterface $e) {
            $this->repeatError($e);
        } finally {
            $this->log('addProvider', [$providerDefinition], null, $timeStart);
        }
    }

    public function delegateLookup(ContainerInterface $container): ContainerInterface
    {
        $this->checkNativeContainer();
        $newContainer = clone $this;
        $newContainer->container = $this->container->delegateLookup($container);

        return $newContainer;
    }

    private function checkNativeContainer(): void
    {
        if (!$this->container instanceof Container) {
            throw new \RuntimeException('This method is for Yiisoft\Di\Container only');
        }
    }
}
