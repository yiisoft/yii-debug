<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug;

class DebuggerIdGenerator
{
    private string $id;

    public function __construct()
    {
        $this->generateId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function reset(): void
    {
        $this->generateId();
    }

    private function generateId(): void
    {
        $this->id = uniqid('yii-debug-', true);
    }
}
