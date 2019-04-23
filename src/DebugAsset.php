<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Debug;

use yii\web\AssetBundle;

/**
 * Debugger asset bundle
 */
class DebugAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@yii/debug/assets';
    /**
     * {@inheritdoc}
     */
    public $css = [
        'css/main.css',
        'css/toolbar.css',
    ];
    /**
     * {@inheritdoc}
     */
    public $js = [
        'js/bs4-native.min.js',
    ];
}
