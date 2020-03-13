<?php

namespace Yiisoft\Yii\Debug;

use Yiisoft\Profiler\Target;

/**
 * ProfileTarget
 */
final class ProfileTarget extends Target
{
    /**
     * @var array complete profiling messages.
     */
    public array $messages = [];
    public function export(array $messages)
    {
        $this->messages = $messages;
    }
}
