# [ConsoleAppInfo collector](./../../../../src/Collector/Console/ConsoleAppInfoCollector.php)

`ConsoleAppInfoCollector` collects information about the application. 

It uses the following events to collect data:
- [`\Yiisoft\Yii\Console\Event\ApplicationStartup`](https://github.com/yiisoft/yii-console/blob/master/src/Event/ApplicationStartup.php)
- [`\Yiisoft\Yii\Console\Event\ApplicationShutdown`](https://github.com/yiisoft/yii-console/blob/master/src/Event/ApplicationShutdown.php)
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
    "applicationProcessingTime": 0.016509056091308594,
    "preloadTime": -0.014687061309814453,
    "applicationEmit": 0.0016329288482666016,
    "requestProcessingTime": 0.00018906593322753906,
    "memoryPeakUsage": 16445144,
    "memoryUsage": 16347512
}
```

### Summary

```json
{
    "console": {
        "php": {
            "version": "8.2.8"
        },
        "request": {
            "startTime": 1704625438.335058,
            "processingTime": 0.00018906593322753906
        },
        "memory": {
            "peakUsage": 20338080
        }
    }
}
```
