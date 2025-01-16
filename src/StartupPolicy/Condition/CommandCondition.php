<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\StartupPolicy\Condition;

use Yiisoft\Strings\WildcardPattern;
use Yiisoft\Yii\Console\Event\ApplicationStartup;

final class CommandCondition implements ConditionInterface
{
    public function __construct(
        /**
         * @var string[]
         * @psalm-var list<string>
         */
        private readonly array $commands,
    ) {
    }

    public function match(object $event): bool
    {
        if (!$event instanceof ApplicationStartup) {
            return false;
        }

        $command = (string) $event->commandName;

        foreach ($this->commands as $pattern) {
            if ((new WildcardPattern($pattern))->match($command)) {
                return true;
            }
        }

        return false;
    }
}
