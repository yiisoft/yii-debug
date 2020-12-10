<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

final class CommandCollector implements CollectorInterface, IndexCollectorInterface
{
    use CollectorTrait;

    private array $commands = [];

    public function getCollected(): array
    {
        return $this->commands;
    }

    public function collect(object $event): void
    {
        if (!is_object($event) || !$this->isActive()) {
            return;
        }

        if ($event instanceof ConsoleCommandEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => $event->getInput()->__toString(),
                'output' => $event->getOutput()->fetch(),
            ];
        }
        if ($event instanceof ConsoleErrorEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => $event->getInput()->__toString(),
                'output' => $event->getOutput()->fetch(),
                'error' => $event->getError()->getMessage(),
                'exitCode' => $event->getExitCode(),
            ];
        }
        if ($event instanceof ConsoleTerminateEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => $event->getInput()->__toString(),
                'output' => $event->getOutput()->fetch(),
                'exitCode' => $event->getExitCode(),
            ];
        }
    }

    public function getIndexData(): array
    {
        $command = $this->commands[ConsoleCommandEvent::class];
        return [
            'command' => $command['input'],
            'commandClass' => get_class($command['command']),
        ];
    }

    private function reset(): void
    {
        $this->commands = [];
    }
}
