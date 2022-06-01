<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Strings\WildcardPattern;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Storage\StorageInterface;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class Debugger
{
    /**
     * @var CollectorInterface[]
     */
    private array $collectors;
    private bool $skipCollect = false;
    private array $optionalRequests;
    private StorageInterface $target;
    private DebuggerIdGenerator $idGenerator;

    public function __construct(
        DebuggerIdGenerator $idGenerator,
        StorageInterface $target,
        array $collectors,
        array $optionalRequests = []
    ) {
        $this->collectors = $collectors;
        $this->optionalRequests = $optionalRequests;
        $this->target = $target;
        $this->idGenerator = $idGenerator;
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

        $this->idGenerator->reset();
        foreach ($this->collectors as $collector) {
            $this->target->addCollector($collector);
            $collector->startup();
        }
    }

    private function isOptionalRequest(ServerRequestInterface $request): bool
    {
        $path = $request
            ->getUri()
            ->getPath();
        foreach ($this->optionalRequests as $pattern) {
            if ((new WildcardPattern($pattern))->match($path)) {
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
