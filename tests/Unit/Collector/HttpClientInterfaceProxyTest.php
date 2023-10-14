<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Yiisoft\Yii\Debug\Collector\HttpClientCollector;
use Yiisoft\Yii\Debug\Collector\HttpClientInterfaceProxy;
use Yiisoft\Yii\Debug\Collector\TimelineCollector;

final class HttpClientInterfaceProxyTest extends TestCase
{
    public function testSendRequest()
    {
        $request = new Request('GET', 'http://example.com');
        $response = new Response(200, [], 'test');

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);
        $collector = new HttpClientCollector(new TimelineCollector());
        $collector->startup();

        $proxy = new HttpClientInterfaceProxy($client, $collector);

        $newResponse = $proxy->sendRequest($request);

        $this->assertSame($newResponse, $response);
        $this->assertCount(1, $collector->getCollected());
    }
}
