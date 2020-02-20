<?php

namespace Yiisoft\Yii\Debug\Collector;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Yii\Debug\Event\RequestEndEvent;
use Yiisoft\Yii\Debug\Event\RequestStartedEvent;

class RequestCollector implements CollectorInterface, MiddlewareInterface, ListenerProviderInterface
{
    use CollectorTrait;

    private ?ServerRequestInterface $request = null;
    private ?ResponseInterface $response = null;
    private ListenerProviderInterface $listenerProvider;
    private float $start = 0;
    private float $stop = 0;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    public function collect(): array
    {
        return [
            $this->request,
            $this->response,
            'processing_time' => $this->stop - $this->start,
        ];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request = clone $request;

        return $this->response = $handler->handle($request);
    }

    public function getListenersForEvent(object $event): iterable
    {
        if ($this->isActive()) {
            if ($event instanceof RequestStartedEvent) {
                $this->start = microtime(true);
            } elseif ($event instanceof RequestEndEvent) {
                $this->stop = microtime(true);
            }
        }

        yield from $this->listenerProvider->getListenersForEvent($event);
    }
}
