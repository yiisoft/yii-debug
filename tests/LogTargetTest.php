<?php

namespace yiiunit\debug;

use yii\helpers\Yii;
use yii\debug\LogTarget;
use yii\debug\Module;

class LogTargetTest extends TestCase
{
    public function testGetRequestTime()
    {
        $logger = $this->getMockBuilder(\yii\log\Logger::class)
            ->setMethods(['dispatch'])
            ->getMock();
        Yii::setLogger($logger);

        $this->app->getRequest()->setUrl('dummy');

        $module = new Module('debug');
        $module->bootstrap($this->app);

        $logTarget = new LogTarget($module);
        $data = $this->invoke($logTarget, 'collectSummary');
        self::assertSame($_SERVER['REQUEST_TIME_FLOAT'], $data['time']);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mockWebApplication();
    }
}
