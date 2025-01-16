<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use LogicException;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;
use Yiisoft\Yii\Debug\StartupPolicy\Collector\CollectorPolicyInterface;
use Yiisoft\Yii\Debug\StartupPolicy\StartupPreventionPolicy;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

/**
 * @psalm-type BacktraceType = list<array{file?:string,line?:int,function?:string,class?:class-string,object?:object,type?:string,args?:array}>
 */
final class Debugger
{
    /**
     * @psalm-var array<string, CollectorInterface>
     */
    private readonly array $collectors;
    private readonly DataNormalizer $dataNormalizer;

    /**
     * @var string|null ID of the current request. Null if debugger is not active.
     */
    private ?string $id = null;

    /**
     * @param CollectorInterface[] $collectors
     */
    public function __construct(
        private readonly StorageInterface $storage,
        array $collectors,
        private readonly ?StartupPreventionPolicy $startupPreventionPolicy = null,
        private readonly ?CollectorPolicyInterface $collectorPolicy = null,
        array $excludedClasses = [],
    ) {
        $preparedCollectors = [];
        foreach ($collectors as $collector) {
            $preparedCollectors[$collector->getName()] = $collector;
        }
        $this->collectors = $preparedCollectors;

        $this->dataNormalizer = new DataNormalizer($excludedClasses);

        register_shutdown_function([$this, 'shutdown']);
    }

    public function isActive(): bool
    {
        return $this->id !== null;
    }

    public function getId(): string
    {
        return $this->id ?? throw new LogicException('Debugger is not started.');
    }

    public function startup(object $event): void
    {
        if ($this->startupPreventionPolicy?->shouldPrevent($event) === true) {
            return;
        }

        $this->id = str_replace('.', '', uniqid('', true));

        foreach ($this->collectors as $collector) {
            if ($this->collectorPolicy?->shouldStartup($collector, $event) !== false) {
                $collector->startup();
            }
        }
    }

    public function shutdown(): void
    {
        if (!$this->isActive()) {
            return;
        }

        try {
            $collectedData = array_map(
                static fn (CollectorInterface $collector) => $collector->getCollected(),
                $this->collectors
            );

            /** @var array[] $data */
            [$data, $objectsMap] = $this->dataNormalizer->prepareDataAndObjectsMap($collectedData, 30);

            /** @var array $summary */
            $summary = $this->dataNormalizer->prepareData($this->collectSummaryData(), 30);

            $this->storage->write($this->getId(), $data, $objectsMap, $summary);
        } finally {
            foreach ($this->collectors as $collector) {
                $collector->shutdown();
            }
            $this->id = null;
        }
    }

    public function stop(): void
    {
        if (!$this->isActive()) {
            return;
        }

        foreach ($this->collectors as $collector) {
            $collector->shutdown();
        }
        $this->id = null;
    }

    /**
     * Collects summary data of current request.
     */
    private function collectSummaryData(): array
    {
        $summaryData = [
            'id' => $this->getId(),
            'collectors' => array_keys($this->collectors),
        ];

        foreach ($this->collectors as $collector) {
            if ($collector instanceof SummaryCollectorInterface) {
                $summaryData = [...$summaryData, ...$collector->getSummary()];
            }
        }

        return $summaryData;
    }
}
