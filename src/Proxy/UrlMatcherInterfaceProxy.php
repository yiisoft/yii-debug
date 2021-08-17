<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Router\MatchingResult;
use Yiisoft\Router\UrlMatcherInterface;
use Yiisoft\Yii\Debug\Collector\RouterCollectorInterface;

final class UrlMatcherInterfaceProxy implements UrlMatcherInterface
{
    private UrlMatcherInterface $urlMatcher;
    private RouterCollectorInterface $routerCollector;

    public function __construct(UrlMatcherInterface $urlMatcher, RouterCollectorInterface $routerCollector)
    {
        $this->urlMatcher = $urlMatcher;
        $this->routerCollector = $routerCollector;
    }

    public function match(ServerRequestInterface $request): MatchingResult
    {
        $timeStart = microtime(true);
        $result = $this->urlMatcher->match($request);
        $this->routerCollector->collect(microtime(true) - $timeStart);

        return $result;
    }
}
