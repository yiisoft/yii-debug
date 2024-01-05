<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Collector\Stream;

use PHPUnit\Framework\TestCase;
use Yiisoft\Yii\Debug\Collector\Stream\HttpStreamProxy;

final class HttpStreamProxyTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        HttpStreamProxy::unregister();
    }

    public function testRegisteredTwice()
    {
        HttpStreamProxy::unregister();
        $this->assertFalse(HttpStreamProxy::$registered);
        HttpStreamProxy::register();
        $this->assertTrue(HttpStreamProxy::$registered);
        HttpStreamProxy::register();
        $this->assertTrue(HttpStreamProxy::$registered);
    }
}
