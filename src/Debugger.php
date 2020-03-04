<?php

namespace Yiisoft\Yii\Debug;

use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class Debugger
{
    private static ?string $id = null;
    /**
     * @var \Yiisoft\Yii\Debug\Collector\CollectorInterface[]
     */
    private array $collectors;
    private StorageInterface $target;

    public function __construct(StorageInterface $target, array $collectors)
    {
        $this->collectors = $collectors;
        $this->target = $target;
    }

    public static function getId(): string
    {
        self::$id = self::$id ?? uniqid('yii-debug-', true);
        return self::$id;
    }

    public function startup(): void
    {
        foreach ($this->collectors as $collector) {
            $this->target->addCollector($collector);
            $collector->startup();
        }
    }

    public function shutdown(): void
    {
        foreach ($this->collectors as $collector) {
            $collector->shutdown();
        }
        $this->target->flush();
    }
}
