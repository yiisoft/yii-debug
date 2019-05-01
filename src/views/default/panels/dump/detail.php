<?php

use Yiisoft\Yii\DataView\GridView;
use yii\helpers\Html;
use yii\helpers\VarDumper;

/* @var $panel Yiisoft\Yii\Debug\Panels\DumpPanel */
/* @var $searchModel Yiisoft\Yii\Debug\Models\Search\Log */
/* @var $dataProvider yii\data\ArrayDataProvider */
?>
    <h1>Dump</h1>
<?php

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'dump-panel-detailed-grid',
    'options' => ['class' => 'detail-grid-view table-responsive'],
    'filterModel' => $searchModel,
    'filterUrl' => $panel->getUrl(),
    'columns' => [
        'category',
        [
            'attribute' => 'message',
            'value' => function ($data) use ($panel) {
                $message = VarDumper::dumpAsString($data['message'], $panel->depth, $panel->highlight);
                //don't encode highlighted variables
                if (!$panel->highlight) {
                    $message = Html::encode($message);
                }

                if (!empty($data['trace'])) {
                    $message .= Html::ul($data['trace'], [
                        'class' => 'trace',
                        'item' => function ($trace) use ($panel) {
                            return '<li>' . $panel->getTraceLine($trace) . '</li>';
                        },
                    ]);
                }

                return $message;
            },
            'format' => 'raw',
            'options' => [
                'width' => '80%',
            ],
        ],
    ],
]);
