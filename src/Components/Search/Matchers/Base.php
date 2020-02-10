<?php
namespace Yiisoft\Yii\Debug\Components\Search\Matchers;

use yii\base\Component;

/**
 * Base class for matchers that are used in a filter.
 *
 * @author Mark Jebri <mark.github@yandex.ru>
 * @since 2.0
 */
abstract class Base extends Component implements MatcherInterface
{
    /**
     * @var mixed base value to check
     */
    protected $baseValue;
    public function setValue($value)
    {
        $this->baseValue = $value;
    }
    public function hasValue()
    {
        return !empty($this->baseValue) || ($this->baseValue === '0');
    }
}
