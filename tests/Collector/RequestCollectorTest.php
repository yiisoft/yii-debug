<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;

final class RequestCollectorTest extends CollectorTestCase
{
    /**
     * @param \Yiisoft\Yii\Debug\Collector\CollectorInterface|RequestCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $requestMock->method('getMethod')
            ->willReturn('GET');
        $requestMock->method('getUri')
            ->willReturn('http://test.site/url');
        $responseMock->method('getStatusCode')
            ->willReturn(200);
        $collector->collect(new BeforeRequest($requestMock));
        $collector->collect(new AfterRequest($responseMock));
    }

    protected function getCollector(): CollectorInterface
    {
        return new RequestCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);
        $data = $collector->getCollected();

        $this->assertEquals('http://test.site/url', $data['request_url']);
        $this->assertEquals('GET', $data['request_method']);
        $this->assertEquals(200, $data['response_status_code']);
    }
}
