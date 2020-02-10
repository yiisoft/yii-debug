<?php
namespace Yiisoft\Yii\Debug\Models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yiisoft\Db\ActiveRecord;

/**
 * Search model for implementation of IdentityInterface
 *
 * @author Semen Dubina <yii2debug@sam002.net>
 * @since 2.0.10
 */
class User extends Model
{
    /**
     * @var Model implementation of IdentityInterface
     */
    public $identityImplement = null;
    public function init()
    {
        if ($this->app->user && $this->app->user->identityClass) {
            $identityImplementation = new $this->app->user->identityClass();
            if ($identityImplementation instanceof Model) {
                $this->identityImplement = $identityImplementation;
            }
        }
        parent::init();
    }
    public function __get($name)
    {
        return $this->identityImplement->__get($name);
    }
    public function __set($name, $value)
    {
        return $this->identityImplement->__set($name, $value);
    }
    public function rules()
    {
        return [[array_keys($this->identityImplement->getAttributes()), 'safe']];
    }
    public function attributes()
    {
        return $this->identityImplement->attributes();
    }
    public function search($params)
    {
        if ($this->identityImplement instanceof ActiveRecord) {
            return $this->searchActiveDataProvider($params);
        }

        return null;
    }

    /**
     * Search method for ActiveRecord
     * @param array $params the data array to load model.
     * @return ActiveDataProvider
     */
    private function searchActiveDataProvider($params)
    {
        /** @var ActiveRecord $model */
        $model = $this->identityImplement;
        $query = $model::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        foreach ($model::getTableSchema()->columns as $attribute => $column) {
            if ($column->phpType === 'string') {
                $query->andFilterWhere(['like', $attribute, $model->getAttribute($attribute)]);
            } else {
                $query->andFilterWhere([$attribute => $model->getAttribute($attribute)]);
            }
        }

        return $dataProvider;
    }
}
