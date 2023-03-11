<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Collector;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\Web\WebAppInfoCollector;
use Yiisoft\Yii\Http\Event\AfterRequest;
use Yiisoft\Yii\Http\Event\BeforeRequest;

use function sleep;
use function usleep;

final class WebAppInfoCollectorTest extends AbstractCollectorTestCase
{
    /**
     * @param CollectorInterface|WebAppInfoCollector $collector
     */
    protected function collectTestData(CollectorInterface $collector): void
    {
        $requestMock = $this->createMock(ServerRequestInterface::class);
        $requestMock->method('getAttribute')->willReturn(microtime(true));
        $collector->collect(new BeforeRequest($requestMock));

        DIRECTORY_SEPARATOR === '\\' ? sleep(1) : usleep(123_000);

        $collector->collect(new AfterRequest($this->createMock(ResponseInterface::class)));
    }

    protected function getCollector(): CollectorInterface
    {
        return new WebAppInfoCollector();
    }

    protected function checkCollectedData(array $data): void
    {
        parent::checkCollectedData($data);

        $this->assertGreaterThan(0.122, $data['requestProcessingTime']);
    }
}
