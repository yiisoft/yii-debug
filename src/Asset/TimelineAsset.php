<?php

namespace Yiisoft\Yii\Debug\Asset;

use Yiisoft\Assets\AssetBundle;

class TimelineAsset extends AssetBundle
{
    public ?string $sourcePath = '@Yiisoft/Yii/Debug/assets';
    public array $css = [
        'css/timeline.css',
    ];
    public array $js = [
        'js/timeline.js',
    ];
    public array $depends = [
        DebugAsset::class
    ];
}
