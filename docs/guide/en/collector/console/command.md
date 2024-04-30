# [Command collector](./../../../../src/Collector/Console/CommandCollector.php)

`CommandCollector` collects all data about an executed console command.

It uses the following events to collect data:
- [`\Symfony\Component\Console\Event\ConsoleCommandEvent`](https://github.com/symfony/symfony/blob/7.1/src/Symfony/Component/Console/Event/ConsoleCommandEvent.php)
- [`\Symfony\Component\Console\Event\ConsoleTerminateEvent`](https://github.com/symfony/symfony/blob/7.1/src/Symfony/Component/Console/Event/ConsoleTerminateEvent.php)
- [`\Symfony\Component\Console\Event\ConsoleErrorEvent`](https://github.com/symfony/symfony/blob/7.1/src/Symfony/Component/Console/Event/ConsoleErrorEvent.php)

## Collected data

### Common

Example:

`console/commands.php`

```php
return [
    'app:hello' => \App\Command\HelloCommand::class,
];
```

`App\Command\HelloCommand`

```php
final class HelloCommand extends Command
{
    protected static $defaultName = 'app:hello';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello world!');
        return 0;
    }
}
```

Output:

```json
{
    "Symfony\\Component\\Console\\Event\\ConsoleCommandEvent": {
        "name": "app:hello",
        "command": "object@App\\Command\\HelloCommand#8454",
        "input": "'app:hello'",
        "output": "",
        "arguments": {
            "command": "object@Symfony\\Component\\Console\\Input\\InputArgument#8532"
        },
        "options": {
            "help": "object@Symfony\\Component\\Console\\Input\\InputOption#8523",
            "quiet": "object@Symfony\\Component\\Console\\Input\\InputOption#8520",
            "verbose": "object@Symfony\\Component\\Console\\Input\\InputOption#8524",
            "version": "object@Symfony\\Component\\Console\\Input\\InputOption#8522",
            "ansi": "object@Symfony\\Component\\Console\\Input\\InputOption#8521",
            "no-interaction": "object@Symfony\\Component\\Console\\Input\\InputOption#8517",
            "config": "object@Symfony\\Component\\Console\\Input\\InputOption#40"
        }
    },
    "Symfony\\Component\\Console\\Event\\ConsoleTerminateEvent": {
        "name": "app:hello",
        "command": "object@App\\Command\\HelloCommand#8454",
        "input": "'app:hello'",
        "output": "Hello world!\n",
        "exitCode": 0
    }
}
```

### Summary

```json
{
    "command": {
        "name": "app:hello",
        "class": "App\\Command\\HelloCommand",
        "input": "'app:hello'",
        "exitCode": 0
    }
}
```
