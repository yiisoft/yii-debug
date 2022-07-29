<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
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
                'name' => $event->getInput()->getFirstArgument() ?? '',
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
                'name' => $event->getCommand()->getName(),
                'command' => $event->getCommand(),
                'input' => $event->getInput()->__toString(),
                'output' => $event->getOutput()->fetch(),
                'exitCode' => $event->getExitCode(),
            ];
            return;
        }

        if ($event instanceof ConsoleEvent) {
            $this->commands[get_class($event)] = [
                'name' => $event->getCommand()->getName(),
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

        $commandEvent = null;
        foreach ($eventTypes as $eventType) {
            if (!array_key_exists($eventType, $this->commands)) {
                continue;
            }

            $commandEvent = $this->commands[$eventType];
            break;
        }

        if ($commandEvent === null) {
            $types = array_keys($this->commands);
            throw new RuntimeException(
                sprintf(
                    'Unsupported event type encountered among "%s". Supported only "%s"',
                    implode('", "', $types),
                    implode('", "', $eventTypes),
                )
            );
        }

        return [
            'command' => [
                'name' => $commandEvent['name'],
                'class' => $commandEvent['command'] instanceof Command ? get_class($commandEvent['command']) : null,
                'input' => $commandEvent['input'],
            ],
        ];
    }

    private function reset(): void
    {
        $this->commands = [];
    }
}
