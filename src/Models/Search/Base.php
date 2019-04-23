<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Debug\Models\Search;

use yii\base\Model;
use Yiisoft\Debug\Components\Search\Filter;
use Yiisoft\Debug\Components\Search\Matchers;

/**
 * Base search model
 *
 * @author Mark Jebri <mark.github@yandex.ru>
 * @since 2.0
 */
class Base extends Model
{
    /**
     * Adds filtering condition for a given attribute
     *
     * @param Filter $filter filter instance
     * @param string $attribute attribute to filter
     * @param bool $partial if partial match should be used
     */
    public function addCondition(Filter $filter, $attribute, $partial = false)
    {
        $value = $this->$attribute;

        if (mb_strpos($value, '>') !== false) {
            $value = (int) str_replace('>', '', $value);
            $filter->addMatcher($attribute, new matchers\GreaterThan(['value' => $value]));
        } elseif (mb_strpos($value, '<') !== false) {
            $value = (int) str_replace('<', '', $value);
            $filter->addMatcher($attribute, new matchers\LowerThan(['value' => $value]));
        } else {
            $filter->addMatcher($attribute, new matchers\SameAs(['value' => $value, 'partial' => $partial]));
        }
    }
}
