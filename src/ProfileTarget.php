<?php

declare(strict_types=1);

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
    public function export(array $messages): void
    {
        $this->messages = $messages;
    }
}
