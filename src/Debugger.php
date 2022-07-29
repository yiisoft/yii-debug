<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Strings\WildcardPattern;
use Yiisoft\Yii\Console\Event\ApplicationStartup;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class Debugger
{
    private bool $skipCollect = false;

    public function __construct(
        private DebuggerIdGenerator $idGenerator,
        private StorageInterface $target,
        /**
         * @var CollectorInterface[]
         */
        private array $collectors,
        private array $optionalRequests = [],
        private array $ignoredCommands = [],
    ) {
    }

    public function getId(): string
    {
        return $this->idGenerator->getId();
    }

    public function startup(object $event): void
    {
        if ($event instanceof BeforeRequest && $this->isOptionalRequest($event->getRequest())) {
            $this->skipCollect = true;
            return;
        }

        if ($event instanceof ApplicationStartup && $this->isCommandIgnored($event->commandName)) {
            $this->skipCollect = true;
            return;
        }

        $this->idGenerator->reset();
        foreach ($this->collectors as $collector) {
            $this->target->addCollector($collector);
            $collector->startup();
        }
    }

    private function isOptionalRequest(ServerRequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();
        foreach ($this->optionalRequests as $pattern) {
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
        foreach ($this->ignoredCommands as $pattern) {
            if ((new WildcardPattern($pattern))->match($command)) {
                return true;
            }
        }
        return false;
    }

    public function shutdown(): void
    {
        try {
            if (!$this->skipCollect) {
                $this->target->flush();
            }
        } finally {
            foreach ($this->collectors as $collector) {
                $collector->shutdown();
            }
            $this->skipCollect = false;
        }
    }

    /**
     * @param array $optionalRequests Patterns for optional request URLs.
     *
     * @see WildcardPattern
     *
     * @return self
     */
    public function withOptionalRequests(array $optionalRequests): self
    {
        $new = clone $this;
        $new->optionalRequests = $optionalRequests;
        return $new;
    }
}
