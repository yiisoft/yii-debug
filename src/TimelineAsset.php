<?php
namespace Yiisoft\Yii\Debug;

use yii\web\AssetBundle;

/**
 * Timeline asset bundle
 *
 * @author Dmitriy Bashkarev <dmitriy@bashkarev.com>
 * @since 2.0.7
 */
class TimelineAsset extends AssetBundle
{
    public $sourcePath = '@Yiisoft/Yii/Debug/assets';
    public $css = [
        'css/timeline.css',
    ];
    public $js = [
        'js/timeline.js',
    ];
    public $depends = [
        DebugAsset::class
    ];
}
