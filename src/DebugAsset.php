<?php
namespace Yiisoft\Yii\Debug;

use yii\web\AssetBundle;

/**
 * Debugger asset bundle
 */
class DebugAsset extends AssetBundle
{
    public $sourcePath = '@Yiisoft/Yii/Debug/assets';
    public $css = [
        'css/main.css',
        'css/toolbar.css',
    ];
    public $js = [
        'js/bs4-native.min.js',
    ];
}
