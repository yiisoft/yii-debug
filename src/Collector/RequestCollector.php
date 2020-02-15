<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Yii\Debug\Event\RequestEndEvent;
use Yiisoft\Yii\Debug\Event\RequestStartedEvent;
use Yiisoft\Yii\Debug\Target\TargetInterface;

class RequestCollector implements CollectorInterface, MiddlewareInterface, ListenerProviderInterface
{
    private ?ServerRequestInterface $request = null;
    private ?ResponseInterface $response = null;
    private TargetInterface $target;
    private ListenerProviderInterface $listenerProvider;
    private float $start = 0;
    private float $stop = 0;

    public function __construct(TargetInterface $target, ListenerProviderInterface $listenerProvider)
    {
        $this->target = $target;
        $this->listenerProvider = $listenerProvider;
    }

    public function export(): void
    {
        $this->target->add($this->request, $this->response, $this->stop - $this->start);
    }

    public function setTarget(TargetInterface $target): void
    {
        $this->target = $target;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request = clone $request;

        return $this->response = $handler->handle($request);
    }

    public function getListenersForEvent(object $event): iterable
    {
        if ($event instanceof RequestStartedEvent) {
            $this->start = microtime(true);
        } elseif ($event instanceof RequestEndEvent) {
            $this->stop = microtime(true);
        }

        yield from $this->listenerProvider->getListenersForEvent($event);
    }
}
