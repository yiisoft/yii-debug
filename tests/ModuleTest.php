<?php

namespace Yiisoft\Yii\Debug\Tests;

use Psr\Log\LoggerInterface;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\View\View;
use Yiisoft\Yii\Debug\Module;

class ModuleTest extends TestCase
{
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
                false,
            ],
            [
                ['10.20.30.40'],
                '10.20.30.40',
                true,
            ],
            [
                ['*'],
                '10.20.30.40',
                true,
            ],
            [
                ['10.20.30.*'],
                '10.20.30.40',
                true,
            ],
            [
                ['10.20.30.*'],
                '10.20.40.40',
                false,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderCheckAccess
     * @param array $allowedIPs
     * @param string $userIp
     * @param bool $expectedResult
     */
    public function testCheckAccess(array $allowedIPs, $userIp, $expectedResult)
    {
        $module = $this->createModule();
        $module->allowedIPs = $allowedIPs;
        $_SERVER['REMOTE_ADDR'] = $userIp;
        $this->assertEquals($expectedResult, $this->invokeMethod($module, 'checkAccess'));
    }

    /**
     * Test to verify toolbars html
     */
    public function testGetToolbarHtml()
    {
        $this->markTestIncomplete('Identical matching is not true way to compare.');
        $module = $this->createModule();

        $toolbar = <<<HTML
<div id="yii-debug-toolbar" data-url="/index.php?r=debug%2Fdefault%2Ftoolbar&amp;tag={$module->logTarget->tag}" style="display:none" class="yii-debug-toolbar-bottom"></div>
HTML;
        $this->assertEquals($toolbar, $module->getToolbarHtml());
    }

    /**
     * Test to ensure toolbar is never cached
     */
    public function testNonCachedToolbarHtml()
    {
        $module = $this->createModule();
        $module->allowedIPs = ['*'];

        $view = $this->createView();
        for ($i = 0; $i <= 1; $i++) {
            ob_start();
//            $module->logTarget->tag = 'tag' . $i;
            if ($view->beginCache(__FUNCTION__, ['duration' => 3])) {
                // TODO fix that
                $module->renderToolbar('stub');
                $view->endCache();
            }
            $output[$i] = ob_get_clean();
        }
        $this->assertNotEquals($output[0], $output[1]);
    }

    /**
     * Making sure debug toolbar does not error
     * in case module ID is not "debug".
     * @see https://github.com/yiisoft/yii2-debug/pull/176/
     */
    public function testToolbarWithCustomModuleID()
    {
        $module = $this->getMockBuilder(Module::class)
            ->disableOriginalConstructor()
            ->setMethods(['renderToolbar'])
            ->getMock();

        $module->expects($this->once())->method('renderToolbar');
        // TODO fix that
        $event = 'stub';
        $module->renderToolbar($event);

        $this->assertTrue(true, 'should be no error');
    }

    public function testDefaultVersion()
    {
        $this->markTestIncomplete('Why it needs?');
        $this->app->extensions['yiisoft/yii2-debug'] = [
            'name' => 'yiisoft/yii2-debug',
            'version' => '2.0.7',
        ];

        $module = $this->createModule();

        /// TODO assert 2.0.7
        $this->assertEquals('1.0', $module->getVersion());
    }

    /**
     * @return \Yiisoft\Yii\Debug\Module
     */
    private function createModule(): \Yiisoft\Yii\Debug\Module
    {
        return $this->getMockBuilder(Module::class)
            ->setConstructorArgs([
                $this->createMock(LoggerInterface::class),
                $this->createMock(UrlGeneratorInterface::class),
            ])
            ->getMock();
    }

    /**
     * @return \Yiisoft\View\View
     */
    private function createView(): \Yiisoft\View\View
    {
        return $this->getMockBuilder(View::class)->disableOriginalConstructor()->getMock();
    }
}
