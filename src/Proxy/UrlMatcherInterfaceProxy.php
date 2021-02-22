<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Proxy;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Yiisoft\Router\MatchingResult;
use Yiisoft\Router\Route;
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

    /**
     * @codeCoverageIgnore
     */
    public function getCurrentRoute(): ?Route
    {
        return $this->urlMatcher->getCurrentRoute();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getCurrentUri(): ?UriInterface
    {
        return $this->urlMatcher->getCurrentUri();
    }
}
