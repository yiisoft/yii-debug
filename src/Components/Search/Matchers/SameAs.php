<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Debug\Components\Search\Matchers;

use yii\helpers\VarDumper;

/**
 * Checks if the given value is exactly or partially same as the base one.
 *
 * @author Mark Jebri <mark.github@yandex.ru>
 * @since 2.0
 */
class SameAs extends Base
{
    /**
     * @var bool if partial match should be used.
     */
    public $partial = false;


    /**
     * {@inheritdoc}
     */
    public function match($value)
    {
        if (!is_scalar($value)) {
            $value = VarDumper::export($value);
        }
        if ($this->partial) {
            return mb_stripos($value, $this->baseValue, 0, $this->app->encoding) !== false;
        }

        return strcmp(mb_strtoupper($this->baseValue, $this->app->encoding), mb_strtoupper($value, $this->app->encoding)) === 0;
    }
}
