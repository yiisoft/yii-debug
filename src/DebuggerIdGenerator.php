<?php

namespace Yiisoft\Yii\Debug;

class DebuggerIdGenerator
{
    private string $id;

    public function __construct()
    {
        $this->id = uniqid('yii-debug-', true);
    }

    public function getId(): string
    {
        return $this->id;
    }
}
