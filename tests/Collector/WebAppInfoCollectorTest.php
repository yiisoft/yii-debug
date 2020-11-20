<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\WebAppInfoCollector;
use Yiisoft\Yii\Web\Event\AfterRequest;
use Yiisoft\Yii\Web\Event\BeforeRequest;

final class WebAppInfoCollectorTest extends CollectorTestCase
{
    /**
     * @param WebAppInfoCollector|\Yiisoft\Yii\Debug\Collector\CollectorInterface $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getAttribute')
            ->willReturn(\microtime(true));
        $collector->collect(new BeforeRequest($requestMock));
        usleep(123_000);
        $collector->collect(new AfterRequest($this->createMock(ResponseInterface::class)));
    }

    protected function getCollector(): CollectorInterface
    {
        return new WebAppInfoCollector();
    }

    protected function checkCollectedData(CollectorInterface $collector): void
    {
        parent::checkCollectedData($collector);
        $data = $collector->getCollected();

        $this->assertGreaterThan(0.123, $data['request_processing_time']);
    }
}
