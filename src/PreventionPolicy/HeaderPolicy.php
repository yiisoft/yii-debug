<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\PreventionPolicy;

use Yiisoft\Yii\Http\Event\BeforeRequest;

final class HeaderPolicy implements PreventionPolicyInterface
{
    public function __construct(
        private readonly string $headerName = 'X-Debug-Ignore',
    ) {
    }

    public function shouldPrevent(object $event): bool
    {
        if (!$event instanceof BeforeRequest) {
            return false;
        }

        $request = $event->getRequest();

        return $request->hasHeader($this->headerName) && $request->getHeaderLine($this->headerName);
    }
}
