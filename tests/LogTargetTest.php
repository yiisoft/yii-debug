<?php

namespace Yiisoft\Yii\Debug\Tests;

use Psr\Log\LoggerInterface;
use ReflectionMethod;
use Yiisoft\Log\Logger;
use Yiisoft\Yii\Debug\LogTarget;
use Yiisoft\Yii\Debug\Module;

class LogTargetTest extends TestCase
{
    /**
     * @TODO Needs refactor {@see \Yiisoft\Yii\Debug\Module}
     * @throws \ReflectionException
     */
    public function testGetRequestTime()
    {
        $this->markTestIncomplete();

        $module = $this->createMock(Module::class);
        $logTarget = new LogTarget($module);

        $data = $this->invokeMethod($logTarget, 'collectSummary');
        self::assertSame($_SERVER['REQUEST_TIME_FLOAT'], $data['time']);
    }
}
