<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\StartupPolicy\Condition;

use Yiisoft\Strings\WildcardPattern;
use Yiisoft\Yii\Http\Event\BeforeRequest;

final class RequestCondition implements ConditionInterface
{
    public function __construct(
        /**
         * @var string[]
         * @psalm-var list<string>
         */
        private readonly array $uriPaths,
    ) {
    }

    public function match(object $event): bool
    {
        if (!$event instanceof BeforeRequest) {
            return false;
        }

        $path = $event->getRequest()->getUri()->getPath();

        foreach ($this->uriPaths as $pattern) {
            if ((new WildcardPattern($pattern))->match($path)) {
                return true;
            }
        }

        return false;
    }
}
