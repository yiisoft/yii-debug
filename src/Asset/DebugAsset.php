<?php
namespace Yiisoft\Yii\Debug\Asset;

use Yiisoft\Assets\AssetBundle;

class DebugAsset extends AssetBundle
{
    public ?string $sourcePath = '@Yiisoft/Yii/Debug/assets';
    public array $css = [
        'css/main.css',
        'css/toolbar.css',
    ];
    public array $js = [
        'js/bs4-native.min.js',
    ];
}
