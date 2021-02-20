<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Proxy;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Yiisoft\Router\FastRoute\UrlMatcher;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Yiisoft\Router\RouteCollection;
use Yiisoft\Yii\Debug\Collector\RouterCollectorInterface;
use Yiisoft\Yii\Debug\Proxy\UrlMatcherInterfaceProxy;

class UrlMatcherInterfaceProxyTest extends TestCase
{
    public function testMatch(): void
    {
        $matcher = new UrlMatcher(new RouteCollection(Group::create(null, [Route::get('/')])));
        $collector = $this->createMock(RouterCollectorInterface::class);
        $collector->expects($this->once())->method('collect');

        $proxy = new UrlMatcherInterfaceProxy($matcher, $collector);
        $proxy->match(new ServerRequest('GET', '/'));
    }
}
