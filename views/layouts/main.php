<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

Yiisoft\Yii\Debug\Asset\DebugAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="none" />
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(\yii\helpers\Yii::getApp()->controller->module->htmlTitle()) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
