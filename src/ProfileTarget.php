<?php
namespace Yiisoft\Yii\Debug;

use yii\profile\Target;

/**
 * ProfileTarget
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.1.0
 */
class ProfileTarget extends Target
{
    /**
     * @var array complete profiling messages.
     * @see \yii\profile\Profiler::$messages
     */
    public $messages = [];
    public function export(array $messages)
    {
        $this->messages = $messages;
    }
}
