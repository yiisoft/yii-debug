<?php
namespace Yiisoft\Yii\Debug;

use yii\web\AssetBundle;

/**
 * DB asset bundle
 */
class DbAsset extends AssetBundle
{
    public $sourcePath = '@Yiisoft/Yii/Debug/assets';
    public $js = [
        'js/db.js',
    ];
}
