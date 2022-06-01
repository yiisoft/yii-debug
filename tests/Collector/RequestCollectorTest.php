<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\IndexCollectorInterface;
use Yiisoft\Yii\Debug\Collector\RequestCollector;
use Yiisoft\Yii\Http\Event\AfterRequest;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class RequestCollectorTest extends CollectorTestCase
{
    /**
     * @param RequestCollector|\Yiisoft\Yii\Debug\Collector\CollectorInterface $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $responseMock = $this->createMock(ResponseInterface::class);
        $requestMock
            ->method('getMethod')
            ->willReturn('GET');
        $requestMock
            ->method('getUri')
            ->willReturn('http://test.site/url');
        $responseMock
            ->method('getStatusCode')
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
        $this->assertInstanceOf(ServerRequestInterface::class, $collector->getCollected()['request']);
        $this->assertInstanceOf(ResponseInterface::class, $collector->getCollected()['response']);
    }

    protected function checkIndexData(CollectorInterface $collector): void
    {
        parent::checkIndexData($collector);
        if ($collector instanceof IndexCollectorInterface) {
            $data = $collector->getIndexData();

            $this->assertEquals('http://test.site/url', $data['requestUrl']);
            $this->assertEquals('GET', $data['requestMethod']);
            $this->assertEquals(200, $data['responseStatusCode']);
        }
    }
}
