<?php
namespace Yiisoft\Yii\Debug\Components\Search\Matchers;

/**
 * Checks if the given value is lower than the base one.
 *
 * @author Mark Jebri <mark.github@yandex.ru>
 * @since 2.0
 */
class LowerThan extends Base
{
    public function match($value)
    {
        return ($value < $this->baseValue);
    }
}
