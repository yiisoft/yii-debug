<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\PreventionPolicy;

use Yiisoft\Strings\WildcardPattern;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class RequestPolicy implements PreventionPolicyInterface
{
    public function __construct(
        /**
         * @var string[]
         * @psalm-var list<string>
         */
        private readonly array $ignoreUriPaths = [],
    ) {
    }

    public function shouldPrevent(object $event): bool
    {
        if (!$event instanceof BeforeRequest) {
            return false;
        }

        $path = $event->getRequest()->getUri()->getPath();

        foreach ($this->ignoreUriPaths as $pattern) {
            if ((new WildcardPattern($pattern))->match($path)) {
                return true;
            }
        }

        return false;
    }
}
