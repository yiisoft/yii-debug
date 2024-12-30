<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

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
    private bool $active = false;

    /**
     * @psalm-var array<string, CollectorInterface>
     */
    private readonly array $collectors;

    /**
     * @param CollectorInterface[] $collectors
     * @param string[] $ignoredRequests
     * @param string[] $ignoredCommands
     */
    public function __construct(
        private readonly DebuggerIdGenerator $idGenerator,
        private readonly StorageInterface $storage,
        array $collectors,
        private array $ignoredRequests = [],
        private array $ignoredCommands = [],
    ) {
        $preparedCollectors = [];
        foreach ($collectors as $collector) {
            $preparedCollectors[$collector->getName()] = $collector;
        }
        $this->collectors = $preparedCollectors;

        register_shutdown_function([$this, 'shutdown']);
    }

    public function getId(): string
    {
        return $this->idGenerator->getId();
    }

    public function startup(object $event): void
    {
        $this->active = true;
        $this->skipCollect = false;

        if ($event instanceof BeforeRequest && $this->isRequestIgnored($event->getRequest())) {
            $this->skipCollect = true;
            return;
        }

        if ($event instanceof ApplicationStartup && $this->isCommandIgnored($event->commandName)) {
            $this->skipCollect = true;
            return;
        }

        $this->idGenerator->reset();
        foreach ($this->collectors as $collector) {
            $collector->startup();
        }
    }

    public function shutdown(): void
    {
        if (!$this->active) {
            return;
        }

        try {
            if (!$this->skipCollect) {
                $data = array_map(
                    static fn (CollectorInterface $collector) => $collector->getCollected(),
                    $this->collectors
                );
                $this->storage->write($this->idGenerator->getId(), $data, $this->collectSummaryData());
            }
        } finally {
            foreach ($this->collectors as $collector) {
                $collector->shutdown();
            }
            $this->active = false;
        }
    }

    public function stop(): void
    {
        if (!$this->active) {
            return;
        }

        foreach ($this->collectors as $collector) {
            $collector->shutdown();
        }
        $this->active = false;
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
            'id' => $this->idGenerator->getId(),
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
