<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\PreventionPolicy;

use Yiisoft\Strings\WildcardPattern;
use Yiisoft\Yii\Console\Event\ApplicationStartup;

final class CommandPolicy implements PreventionPolicyInterface
{
    public function __construct(
        /**
         * @var string[]
         * @psalm-var list<string>
         */
        private readonly array $ignoreCommands = [],
    ) {
    }

    public function shouldPrevent(object $event): bool
    {
        if (!$event instanceof ApplicationStartup) {
            return false;
        }

        $command = (string) $event->commandName;

        foreach ($this->ignoreCommands as $pattern) {
            if ((new WildcardPattern($pattern))->match($command)) {
                return true;
            }
        }

        return false;
    }
}
