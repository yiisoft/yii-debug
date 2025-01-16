<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\StartupPolicy\Condition;

use Yiisoft\Yii\Http\Event\BeforeRequest;

final class HeaderCondition implements ConditionInterface
{
    public function __construct(
        private readonly string $headerName,
    ) {
    }

    public function match(object $event): bool
    {
        if (!$event instanceof BeforeRequest) {
            return false;
        }

        $request = $event->getRequest();

        return $request->hasHeader($this->headerName) && $request->getHeaderLine($this->headerName);
    }
}
