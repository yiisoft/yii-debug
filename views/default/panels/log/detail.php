<?php

use Psr\Log\LogLevel;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use Yiisoft\Log\Logger;
use Yiisoft\Yii\DataView\GridView;

/* @var $panel Yiisoft\Yii\Debug\Panels\LogPanel */
/* @var $searchModel Yiisoft\Yii\Debug\Models\Search\Log */
/* @var $dataProvider yii\data\ArrayDataProvider */
?>
    <h1>Log Messages</h1>
<?php

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'log-panel-detailed-grid',
    'options' => ['class' => ['detail-grid-view', 'table-responsive', 'logs-messages-table']],
    'filterModel' => $searchModel,
    'filterUrl' => $panel->getUrl(),
    'rowOptions' => function ($model) {
        switch ($model['level']) {
            case LogLevel::ERROR:
                return ['class' => 'danger'];
            case LogLevel::WARNING:
                return ['class' => 'warning'];
            case LogLevel::INFO:
                return ['class' => 'success'];
            default:
                return [];
        }
    },
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
        [
            'attribute' => 'time',
            'value' => function ($data) {
                $timeInSeconds = $data['time'] / 1000;
                $millisecondsDiff = (int) (($timeInSeconds - (int) $timeInSeconds) * 1000);

                return date('H:i:s.', $timeInSeconds) . sprintf('%03d', $millisecondsDiff);
            },
            'headerOptions' => [
                'class' => 'sort-numerical'
            ]
        ],
        [
            'attribute' => 'level',
            'value' => function ($data) {
                return Logger::getLevelName($data['level']);
            },
            'filter' => [
                LogLevel::DEBUG => ' Debug ',
                LogLevel::INFO => ' Info ',
                LogLevel::WARNING => ' Warning ',
                LogLevel::ERROR => ' Error ',
            ],
        ],
        'category',
        [
            'attribute' => 'message',
            'value' => function ($data) use ($panel) {
                $message = Html::encode(is_string($data['message']) ? $data['message'] : VarDumper::export($data['message']));
                if (!empty($data['trace'])) {
                    $message .= Html::ul($data['trace'], [
                        'class' => 'trace',
                        'item' => function ($trace) use ($panel) {
                            return '<li>' . $panel->getTraceLine($trace) . '</li>';
                        }
                    ]);
                }
                return $message;
            },
            'format' => 'raw',
            'options' => [
                'width' => '50%',
            ],
        ],
    ],
]);
