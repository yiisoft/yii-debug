<?php
namespace Yiisoft\Yii\Debug\Asset;

use Yiisoft\Assets\AssetBundle;

class UserswitchAsset extends AssetBundle
{
    public ?string $sourcePath = '@Yiisoft/Yii/Debug/assets';
    public array $js = [
        'js/userswitch.js',
    ];
}
