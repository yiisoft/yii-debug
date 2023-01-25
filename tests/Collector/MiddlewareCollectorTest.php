<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Middleware\Dispatcher\Event\AfterMiddleware;
use Yiisoft\Middleware\Dispatcher\Event\BeforeMiddleware;
use Yiisoft\Middleware\Dispatcher\MiddlewareFactory;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\MiddlewareCollector;
use Yiisoft\Yii\Debug\Tests\Support\DummyMiddleware;

final class MiddlewareCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|\Yiisoft\Yii\Debug\Collector\MiddlewareCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $collector->collect(new BeforeMiddleware($this->createCallableMiddleware(static fn() => 1), new ServerRequest('GET', '/test')));
        $collector->collect(new BeforeMiddleware($this->createCallableMiddleware([DummyMiddleware::class, 'process']), new ServerRequest('GET', '/test')));
        $collector->collect(new BeforeMiddleware(new DummyMiddleware(), new ServerRequest('GET', '/test')));
        $collector->collect(new AfterMiddleware(new DummyMiddleware(), new Response(200)));
        $collector->collect(new AfterMiddleware($this->createCallableMiddleware(static fn() => 1),new Response(200)));
        $collector->collect(new AfterMiddleware($this->createCallableMiddleware([DummyMiddleware::class, 'process']),new Response(200)));
    }

    protected function getCollector(): CollectorInterface
    {
        return new MiddlewareCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);

        $data = $collector->getCollected();

        $this->assertNotEmpty($data['beforeStack']);
        $this->assertNotEmpty($data['afterStack']);
        $this->assertNotEmpty($data['actionHandler']);
        $this->assertEquals(DummyMiddleware::class, $data['actionHandler']['name']);
        $this->assertEquals('GET', $data['actionHandler']['request']->getMethod());
    }

    private function createCallableMiddleware(callable|array $callable): \Psr\Http\Server\MiddlewareInterface
    {
        $factory = new MiddlewareFactory(new Container(ContainerConfig::create()));
        return $factory->create($callable);
    }
}
