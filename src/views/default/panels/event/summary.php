<?php
/* @var $panel Yiisoft\Yii\Debug\Panels\EventPanel */
/* @var $eventCount int */
if ($eventCount): ?>
    <div class="yii-debug-toolbar__block">
        <a href="<?= $panel->getUrl() ?>">Events <span class="yii-debug-toolbar__label"><?= $eventCount ?></span></a>
    </div>
<?php endif ?>
