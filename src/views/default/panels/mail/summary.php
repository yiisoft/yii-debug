<?php
/* @var $panel Yiisoft\Debug\Panels\MailPanel */
/* @var $mailCount int */
if ($mailCount): ?>
    <div class="yii-debug-toolbar__block">
        <a href="<?= $panel->getUrl() ?>">Mail <span class="yii-debug-toolbar__label"><?= $mailCount ?></span></a>
    </div>
<?php endif ?>
