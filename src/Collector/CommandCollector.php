<?php

namespace Yiisoft\Yii\Debug\Collector;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

final class CommandCollector implements CollectorInterface
{
    use CollectorTrait;

    private array $commands = [];

    public function getCollected(): array
    {
        return $this->commands;
    }

    public function collect(object $event): void
    {
        if ($event instanceof ConsoleCommandEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => (string)$event->getInput(),
                'output' => $event->getOutput()->fetch(),
            ];
        }
        if ($event instanceof ConsoleErrorEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => (string)$event->getInput(),
                'output' => $event->getOutput()->fetch(),
                'error' => $event->getError()->getMessage(),
                'exitCode' => $event->getExitCode(),
            ];
        }
        if ($event instanceof ConsoleTerminateEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => (string)$event->getInput(),
                'output' => $event->getOutput()->fetch(),
                'exitCode' => $event->getExitCode(),
            ];
        }
    }

    private function reset(): void
    {
        $this->commands = [];
    }
}
