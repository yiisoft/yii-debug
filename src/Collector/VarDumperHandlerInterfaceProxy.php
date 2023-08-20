<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Yiisoft\VarDumper\HandlerInterface;

final class VarDumperHandlerInterfaceProxy implements HandlerInterface
{
    public function __construct(
        private HandlerInterface $decorated,
        private VarDumperCollector $collector,
    ) {
    }

    public function handle(mixed $variable, int $depth, bool $highlight = false): void
    {
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $callStack = null;
        foreach ($stack as $value) {
            if (!isset($value['file'])) {
                continue;
            }
            if (str_ends_with($value['file'], '/var-dumper/src/functions.php')) {
                continue;
            }
            if (str_ends_with($value['file'], '/var-dumper/src/VarDumper.php')) {
                continue;
            }
            $callStack = $value;
            break;
        }

        $this->collector->collectVar(
            $variable,
            $callStack === null ? '' : $callStack['file'] . ':' . $callStack['line']
        );
        $this->decorated->handle($variable, $depth, $highlight);
    }
}
