<?php

namespace Yiisoft\Yii\Debug;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Yiisoft\EventDispatcher\Dispatcher\CompositeDispatcher;
use Yiisoft\EventDispatcher\Provider\ConcreteProvider;
use Yiisoft\EventDispatcher\Dispatcher\Dispatcher;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Web\Event\ApplicationStartup;
use Yiisoft\Yii\Web\Event\ApplicationShutdown;

final class Debugger
{
    private string $id;
    /**
     * @var \Yiisoft\Yii\Debug\Collector\CollectorInterface[]
     */
    private array $collectors;
    private StorageInterface $target;

    public function __construct(StorageInterface $target, array $collectors)
    {
        $this->collectors = $collectors;
        $this->target = $target;
        $this->id = uniqid('yii-debug-', true);
    }

    public static function register(array $params, ContainerInterface $container): void
    {
        $debugEnabled = (bool)($params['debugger.enabled'] ?? false);
        if ($container->has(self::class) && $debugEnabled) {
            $debugger = $container->get(self::class);

            //The bad practice, think about avoiding this
            $provider = $container->get(ListenerProviderInterface::class);
            $provider->attach(function (ApplicationStartup $event) use ($debugger) {
                $debugger->startup();
            });
            $provider->attach(function (ApplicationShutdown $event) use ($debugger) {
                $debugger->shutdown();
            });

            //The bad practice, think about avoiding this
            $container->addProvider(new DebugServiceProvider());
        }
    }
public
function getId(): string
{
    return $this->id;
}

public
function startup(): void
{
    foreach ($this->collectors as $collector) {
        $this->target->addCollector($collector);
        $collector->startup();
    }
}

public
function shutdown(): void
{
    foreach ($this->collectors as $collector) {
        $collector->shutdown();
    }
    $this->target->flush();
}
}
