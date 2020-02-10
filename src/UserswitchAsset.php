<?php
namespace Yiisoft\Yii\Debug;

use yii\web\AssetBundle;

/**
 * User switch asset bundle
 *
 * @author Semen Dubina <yii2debug@sam002.net>
 * @since 2.0.10
 */
class UserswitchAsset extends AssetBundle
{
    public $sourcePath = '@yii/debug/assets';
    public $js = [
        'js/userswitch.js',
    ];
}
