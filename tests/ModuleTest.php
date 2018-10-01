<?php

namespace yiiunit\debug;

use yii\base\Event;
use yii\cache\Cache;
use yii\cache\FileCache;
use yii\debug\Module;
use yii\tests\TestCase;
use yii\view\View;

class ModuleTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockWebApplication();
    }

    // Tests :

    /**
     * Data provider for [[testCheckAccess()]]
     * @return array test data
     */
    public function dataProviderCheckAccess()
    {
        return [
            [
                [],
                '10.20.30.40',
                false
            ],
            [
                ['10.20.30.40'],
                '10.20.30.40',
                true
            ],
            [
                ['*'],
                '10.20.30.40',
                true
            ],
            [
                ['10.20.30.*'],
                '10.20.30.40',
                true
            ],
            [
                ['10.20.30.*'],
                '10.20.40.40',
                false
            ],
        ];
    }

    /**
     * @dataProvider dataProviderCheckAccess
     *
     * @param array $allowedIPs
     * @param string $userIp
     * @param bool $expectedResult
     */
    public function testCheckAccess(array $allowedIPs, $userIp, $expectedResult)
    {
        $module = new Module('debug', $this->app);
        $module->allowedIPs = $allowedIPs;
        $_SERVER['REMOTE_ADDR'] = $userIp;
        $this->assertEquals($expectedResult, $this->invokeMethod($module, 'checkAccess'));
    }

    /**
     * Test to verify toolbars html
     */
    public function testGetToolbarHtml()
    {
        $logger = $this->getMockBuilder(\yii\log\Logger::class)
            ->setConstructorArgs([[]])
            ->setMethods(['dispatch'])
            ->getMock();
        $this->container->set('logger', $logger);

        $module = new Module('debug', $this->app);
        $module->bootstrap($this->app);

        $this->assertEquals(<<<HTML
<div id="yii-debug-toolbar" data-url="/index.php?r=debug%2Fdefault%2Ftoolbar&amp;tag={$module->logTarget->tag}" style="display:none" class="yii-debug-toolbar-bottom"></div>
HTML
        ,$module->getToolbarHtml());
    }

    /**
     * Test to ensure toolbar is never cached
     */
    public function testNonCachedToolbarHtml()
    {
        $logger = $this->getMockBuilder(\yii\log\Logger::class)
            ->setConstructorArgs([[]])
            ->setMethods(['dispatch'])
            ->getMock();
        $this->container->set('logger', $logger);

        $module = new Module('debug', $this->app);
        $module->allowedIPs = ['*'];
        $this->app->setModule('debug',$module);
        $module->bootstrap($this->app);

        $this->container->set('cache', new Cache([
            '__class' => Cache::class,
            'handler' => new FileCache('@yiiunit/debug/runtime/cache')
        ]));

        $view = $this->app->view;
        for ($i = 0; $i <= 1; $i++) {
            ob_start();
            $module->logTarget->tag = 'tag' . $i;
            if ($view->beginCache(__FUNCTION__, ['duration' => 3])) {
                $module->renderToolbar(new Event('sender', $view));
                $view->endCache();
            }
            $output[$i] = ob_get_clean();
        }
        $this->assertNotEquals($output[0], $output[1]);
    }

    /**
     * Making sure debug toolbar does not error
     * in case module ID is not "debug".
     *
     * @see https://github.com/yiisoft/yii2-debug/pull/176/
     */
    public function testToolbarWithCustomModuleID()
    {
        $logger = $this->getMockBuilder(\yii\log\Logger::class)
            ->setConstructorArgs([[]])
            ->setMethods(['dispatch'])
            ->getMock();
        $this->container->set('logger', $logger);

        $moduleId = 'my_debug';
        $module = new Module($moduleId, $this->app);
        $module->allowedIPs = ['*'];
        $this->app->setModule($moduleId, $module);
        $module->bootstrap($this->app);

        $view = new View($this->app);

        ob_start();
        $module->renderToolbar(new Event('test', $view));
        ob_end_clean();

        $this->assertTrue(true, 'should be no error');
    }

    public function testDefaultVersion()
    {
        $this->app->extensions['yiisoft/yii2-debug'] = [
            'name' => 'yiisoft/yii2-debug',
            'version' => '2.0.7',
        ];

        $module = new Module('debug', $this->app);

        /// TODO assert 2.0.7
        $this->assertEquals('1.0', $module->getVersion());
    }
} 
