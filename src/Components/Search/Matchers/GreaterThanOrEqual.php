<?php
namespace Yiisoft\Yii\Debug\Components\Search\Matchers;

/**
 * Checks if the given value is greater than or equal the base one.
 *
 * @author Dmitriy Bashkarev <dmitriy@bashkarev.com>
 * @since 2.0.7
 */
class GreaterThanOrEqual extends Base
{
    public function match($value)
    {
        return $value >= $this->baseValue;
    }
}
