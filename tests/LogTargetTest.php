<?php

namespace Yiisoft\Yii\Debug\Tests;

use yii\tests\TestCase;
use Yiisoft\Yii\Debug\LogTarget;
use Yiisoft\Yii\Debug\Module;

class LogTargetTest extends TestCase
{
    public function testGetRequestTime()
    {
        $logger = $this->getMockBuilder(\Yiisoft\Log\Logger::class)
            ->setConstructorArgs([[]])
            ->setMethods(['dispatch'])
            ->getMock();
        $this->container->set('logger', $logger);

        $this->app->getRequest()->setUrl('dummy');

        $module = new Module('debug', $this->app);
        $module->bootstrap($this->app);

        $logTarget = new LogTarget($module);
        $data = $this->invokeMethod($logTarget, 'collectSummary');
        self::assertSame($_SERVER['REQUEST_TIME_FLOAT'], $data['time']);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mockWebApplication();
    }
}
