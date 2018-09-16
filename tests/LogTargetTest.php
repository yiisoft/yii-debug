<?php

namespace yiiunit\debug;

use yii\helpers\Yii;
use yii\debug\LogTarget;
use yii\debug\Module;
use yii\tests\TestCase;

class LogTargetTest extends TestCase
{
    public function testGetRequestTime()
    {
        $logger = $this->getMockBuilder(\yii\log\Logger::class)
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
