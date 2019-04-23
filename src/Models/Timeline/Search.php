<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Debug\Models\Timeline;

use Yiisoft\Debug\Components\Search\Filter;
use Yiisoft\Debug\Components\Search\Matchers\GreaterThanOrEqual;
use Yiisoft\Debug\Models\Search\Base;
use Yiisoft\Debug\Panels\TimelinePanel;

/**
 * Search model for timeline data.
 *
 * @author Dmitriy Bashkarev <dmitriy@bashkarev.com>
 * @since 2.0.8
 */
class Search extends Base
{
    /**
     * @var string attribute search
     */
    public $category;
    /**
     * @var int attribute search
     */
    public $duration = 0;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category', 'duration'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'duration' => 'Duration ≥'
        ];
    }

    /**
     * Returns data provider with filled models. Filter applied if needed.
     *
     * @param array $params $params an array of parameter values indexed by parameter names
     * @param TimeLinePanel $panel
     * @return DataProvider
     */
    public function search($params, $panel)
    {
        $models = $panel->models;
        $dataProvider = new DataProvider($panel, [
            'allModels' => $models,
            'sort' => [
                'attributes' => ['category', 'timestamp']
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $filter = new Filter();
        $this->addCondition($filter, 'category', true);
        if ($this->duration > 0) {
            $filter->addMatcher('duration', new GreaterThanOrEqual(['value' => $this->duration / 1000]));
        }
        $dataProvider->allModels = $filter->filter($models);

        return $dataProvider;
    }
}
