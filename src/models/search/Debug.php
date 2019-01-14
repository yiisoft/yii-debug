<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\debug\models\search;

use yii\helpers\Yii;
use yii\data\ArrayDataProvider;
use yii\debug\components\search\Filter;

/**
 * Search model for requests manifest data.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Mark Jebri <mark.github@yandex.ru>
 * @since 2.0
 */
class Debug extends Base
{
    /**
     * @var string tag attribute input search value
     */
    public $tag;
    /**
     * @var string ip attribute input search value
     */
    public $ip;
    /**
     * @var string method attribute input search value
     */
    public $method;
    /**
     * @var int ajax attribute input search value
     */
    public $ajax;
    /**
     * @var string url attribute input search value
     */
    public $url;
    /**
     * @var string status code attribute input search value
     */
    public $statusCode;
    /**
     * @var int sql count attribute input search value
     */
    public $sqlCount;
    /**
     * @var int total mail count attribute input search value
     */
    public $mailCount;
    /**
     * @var array critical codes, used to determine grid row options.
     */
    public $criticalCodes = [400, 404, 500];


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tag', 'ip', 'method', 'ajax', 'url', 'statusCode', 'sqlCount', 'mailCount'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tag' => 'Tag',
            'ip' => 'Ip',
            'method' => 'Method',
            'ajax' => 'Ajax',
            'url' => 'url',
            'statusCode' => 'Status code',
            'sqlCount' => 'Query Count',
            'mailCount' => 'Mail Count',
        ];
    }

    /**
     * Returns data provider with filled models. Filter applied if needed.
     * @param array $params an array of parameter values indexed by parameter names
     * @param array $models data to return provider for
     * @return \yii\data\ArrayDataProvider
     */
    public function search($params, $models)
    {
        $dataProvider = Yii::createObject([
            '__class' => ArrayDataProvider::class,
            'allModels' => $models,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => ['method', 'ip', 'tag', 'time', 'statusCode', 'sqlCount', 'mailCount'],
                'defaultOrder' => [
                    'time' => SORT_DESC,
                ],
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        
        $filter = new Filter();
        $this->addCondition($filter, 'tag', true);
        $this->addCondition($filter, 'ip', true);
        $this->addCondition($filter, 'method');
        $this->addCondition($filter, 'ajax');
        $this->addCondition($filter, 'url', true);
        $this->addCondition($filter, 'statusCode');
        $this->addCondition($filter, 'sqlCount');
        $this->addCondition($filter, 'mailCount');
        $dataProvider->allModels = $filter->filter($models);

        return $dataProvider;
    }

    /**
     * Checks if code is critical.
     *
     * @param int $code
     * @return bool
     */
    public function isCodeCritical($code)
    {
        return in_array($code, $this->criticalCodes, false);
    }
}
