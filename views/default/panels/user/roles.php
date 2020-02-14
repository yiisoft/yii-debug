<?php

use Yiisoft\Yii\DataView\GridView;

/* @var $panel Yiisoft\Yii\Debug\Panels\UserPanel */

if ($panel->data['rolesProvider']) {
    echo '<h2>Roles</h2>';

    echo GridView::widget([
        'dataProvider' => $panel->data['rolesProvider'],
        'pager' => [
            'linkContainerOptions' => [
                'class' => 'page-item'
            ],
            'linkOptions' => [
                'class' => 'page-link'
            ],
            'disabledListItemSubTagOptions' => [
                'tag' => 'a',
                'href' => 'javascript:;',
                'tabindex' => '-1',
                'class' => 'page-link'
            ]
        ],
        'columns' => [
            'name',
            'description',
            'ruleName',
            'data',
            'createdAt:datetime',
            'updatedAt:datetime'
        ]
    ]);
}

if ($panel->data['permissionsProvider']) {
    echo '<h2>Permissions</h2>';

    echo GridView::widget([
        'dataProvider' => $panel->data['permissionsProvider'],
        'pager' => [
            'linkContainerOptions' => [
                'class' => 'page-item'
            ],
            'linkOptions' => [
                'class' => 'page-link'
            ],
            'disabledListItemSubTagOptions' => [
                'tag' => 'a',
                'href' => 'javascript:;',
                'tabindex' => '-1',
                'class' => 'page-link'
            ]
        ],
        'columns' => [
            'name',
            'description',
            'ruleName',
            'data',
            'createdAt:datetime',
            'updatedAt:datetime'
        ]
    ]);
} ?>

