# Collectors

## Service collector

`ServiceCollector` is a collector that listens all tracked services and collects its arguments, results and errors.

By default, the debug extension has enabled [`\Yiisoft\Yii\Debug\Collector\ServiceCollector`](./src/Collector/ServiceCollector.php) with the following settings:
1. Log arguments
2. Log results
3. Log errors

It may degrade the application performance so it may be a good idea to disable it in production.

You may control what is logged by specifying the settings in the configuration:

```php
use Yiisoft\Yii\Debug\Collector\ContainerInterfaceProxy;

return [
    'yiisoft/yii-debug' => [
        // use 0 or ContainerInterfaceProxy::LOG_NOTHING to disable logging
        'logLevel' => ContainerInterfaceProxy::LOG_ARGUMENTS | ContainerInterfaceProxy::LOG_RESULT | ContainerInterfaceProxy::LOG_ERROR,
    ],
];
```
