<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use LogicException;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Strings\WildcardPattern;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Http\Event\BeforeRequest;

/**
 * @psalm-type BacktraceType = list<array{file?:string,line?:int,function?:string,class?:class-string,object?:object,type?:string,args?:array}>
 */
final class Debugger
{
    private bool $skipCollect = false;

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
     * @param string[] $ignoredRequests
     * @param string[] $ignoredCommands
     */
    public function __construct(
        private readonly StorageInterface $storage,
        array $collectors,
        private array $ignoredRequests = [],
        private array $ignoredCommands = [],
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
        $this->id = str_replace('.', '', uniqid('', true));
        $this->skipCollect = false;

        if ($event instanceof BeforeRequest && $this->isRequestIgnored($event->getRequest())) {
            $this->skipCollect = true;
            return;
        }

        if ($event instanceof ApplicationStartup && $this->isCommandIgnored($event->commandName)) {
            $this->skipCollect = true;
            return;
        }

        foreach ($this->collectors as $collector) {
            $collector->startup();
        }
    }

    public function shutdown(): void
    {
        if ($this->id === null) {
            return;
        }

        try {
            if (!$this->skipCollect) {
                $collectedData = array_map(
                    static fn (CollectorInterface $collector) => $collector->getCollected(),
                    $this->collectors
                );

                /** @var array[] $data */
                [$data, $objectsMap] = $this->dataNormalizer->prepareDataAndObjectsMap($collectedData, 30);

                /** @var array $summary */
                $summary = $this->dataNormalizer->prepareData($this->collectSummaryData(), 30);

                $this->storage->write($this->getId(), $data, $objectsMap, $summary);
            }
        } finally {
            foreach ($this->collectors as $collector) {
                $collector->shutdown();
            }
            $this->id = null;
        }
    }

    public function stop(): void
    {
        if ($this->id === null) {
            return;
        }

        foreach ($this->collectors as $collector) {
            $collector->shutdown();
        }
        $this->id = null;
    }

    private function isRequestIgnored(ServerRequestInterface $request): bool
    {
        if ($request->hasHeader('X-Debug-Ignore') && $request->getHeaderLine('X-Debug-Ignore') === 'true') {
            return true;
        }
        $path = $request->getUri()->getPath();
        foreach ($this->ignoredRequests as $pattern) {
            if ((new WildcardPattern($pattern))->match($path)) {
                return true;
            }
        }
        return false;
    }

    private function isCommandIgnored(?string $command): bool
    {
        if ($command === null || $command === '') {
            return true;
        }
        if (getenv('YII_DEBUG_IGNORE') === 'true') {
            return true;
        }
        foreach ($this->ignoredCommands as $pattern) {
            if ((new WildcardPattern($pattern))->match($command)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string[] $ignoredRequests Patterns for ignored request URLs.
     *
     * @see WildcardPattern
     */
    public function withIgnoredRequests(array $ignoredRequests): self
    {
        $new = clone $this;
        $new->ignoredRequests = $ignoredRequests;
        return $new;
    }

    /**
     * @param string[] $ignoredCommands Patterns for ignored commands names.
     *
     * @see WildcardPattern
     */
    public function withIgnoredCommands(array $ignoredCommands): self
    {
        $new = clone $this;
        $new->ignoredCommands = $ignoredCommands;
        return $new;
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
