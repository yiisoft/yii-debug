<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use RuntimeException;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

use function get_class;
use function is_object;

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

        if ($event instanceof ConsoleErrorEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => $event->getInput()->__toString(),
                'output' => $event->getOutput()->fetch(),
                'error' => $event->getError()->getMessage(),
                'exitCode' => $event->getExitCode(),
            ];
            return;
        }

        if ($event instanceof ConsoleTerminateEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => $event->getInput()->__toString(),
                'output' => $event->getOutput()->fetch(),
                'exitCode' => $event->getExitCode(),
            ];
            return;
        }

        if ($event instanceof ConsoleEvent) {
            $this->commands[get_class($event)] = [
                'command' => $event->getCommand(),
                'input' => $event->getInput()->__toString(),
                'output' => $event->getOutput()->fetch(),
            ];
        }
    }

    public function getIndexData(): array
    {
        $eventTypes = [
            ConsoleErrorEvent::class,
            ConsoleTerminateEvent::class,
            ConsoleCommandEvent::class,
        ];

        $command = null;
        foreach ($eventTypes as $eventType) {
            if (!array_key_exists($eventType, $this->commands)) {
                continue;
            }

            $command = $this->commands[$eventType];
            break;
        }

        if ($command === null) {
            $types = array_keys($this->commands);
            throw new RuntimeException('Unsupported event type encountered among "' . implode('", "', $types) . '".');
        }

        return [
            'command.input' => $command['input'],
            'command.class' => $command['command'] !== null ? get_class($command['command']) : null,
        ];
    }

    private function reset(): void
    {
        $this->commands = [];
    }
}
