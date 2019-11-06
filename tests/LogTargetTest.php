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

        $method = new ReflectionMethod(LogTarget::class, 'collectSummary');
        $method->setAccessible(true);

        $module = new Module();
        $logTarget = new LogTarget($module);

        $data = $method->invoke($logTarget);
        self::assertSame($_SERVER['REQUEST_TIME_FLOAT'], $data['time']);
    }
}
