# Collectors

Yii Debug uses a concept named "collectors".
Each collector decides what payload it needs to collect and exports the collected payload in order to save it into storage.

A collector may work either with both HTTP requests and console runs, or individually.
A collector may be either an event listener or a decorator to any service defined in the application DI container configuration.

Take a look at the [`Yiisoft\Yii\Debug\Collector\CollectorInterface`](./src/Collector/CollectorInterface.php):

```php
namespace Yiisoft\Yii\Debug\Collector;

interface CollectorInterface
{
    /**
     * @return string Collector's name.
     */
    public function getName(): string;

    /**
     * Called once at application startup.
     * Any initialization could be done here.
     */
    public function startup(): void;

    /**
     * Called once at application shutdown.
     * Cleanup could be done here.
     */
    public function shutdown(): void;

    /**
     * @return array Data collected.
     */
    public function getCollected(): array;
}
```

Use the trait to reduce the duplication of code and avoid possible bugs: [`\Yiisoft\Yii\Debug\Collector\CollectorTrait`](./src/Collector/CollectorTrait.php)

All you need to create a collector is to implement the interface and register it in the configuration.

### Example

```php
class MyCollector implements \Yiisoft\Yii\Debug\Collector\CollectorInterface
{
    use \Yiisoft\Yii\Debug\Collector\CollectorTrait;
    
    /**
     * Payload collected during a run. 
     */
    private array $data = [];

    public function getCollected() : array
    {
        return $this->data;
    }
}
```

If you implement data collection, it's also a good idea to implement data reset. With `CollectorTrait` it's as simple as adding `reset()` method:
```php
    private function reset(): void
    {
        $this->data = [];
    }
```

You can enable collector in application configuration as follows:

```php
return [
    'yiisoft/yii-debug' => [
        // if you want to register collector both for web requests and console runs
        'collectors' => [
            \App\Debug\AwesomeCollector::class,
        ],
        // if you want to register collector only for web requests
        'collectors.web' => [
            \App\Debug\AwesomeWebCollector::class,
        ],
        // if you want to register collector only for console runs
        'collectors.console' => [
            \App\Debug\AwesomeConsoleCollector::class,
        ],
    ],
];
```

Under `yiisoft/yii-debug` configuration you may use:
1. `collectors` key for both web and console runs
2. `collectors.web` key only for web requests
3. `collectors.console` key only for console runs

> Do not register a collector for a session where the collector will not collect any payload.


The lines above connect collectors with a debug extension run.
Under the hood it calls `getCollected()` method from the collectors at the end of application cycle run.

### Event listener collector

Subscribe to any events you want with adding a listener into the configuration:

With [`yiisoft/event-dispatcher`](https://github.com/yiisoft/event-dispatcher) configuration it may look like the following:

```php
return [
    \Yiisoft\Yii\Http\Event\BeforeRequest::class => [
        [\App\Debug\AwesomeCollector::class, 'collect'],
    ],
];
```

Each `Yiisoft\Yii\Http\Event\BeforeRequest` triggered event will call `App\Debug\AwesomeCollector::collect($event)` method,
so you can collect any data from the event or call any other services to enrich the event data with additional payload.

### Proxy collector

Proxy collectors are used in case you want to decorate a service from DI container and sniff methods' calls with its values.

First you need to create a class that will work as a decorator. See https://en.wikipedia.org/wiki/Decorator_pattern if you are new with it.

Decorators may inject any services through `__construct` method, but you should specify services you like to wrap.
In the section `trackedServices` of `yiisoft/yii-debug` configuration you should specify:

1. A service you want to decorate
2. A decorator that will decorate the service
3. A collector that will be injected into the decorator

Syntax of configuration is: `Decorating service => [Decorator, Collector]`.

Despite adding the tracking service configuration you still need to register the collector if you did not do it before.
Whole configuration of added proxy collector looks like the following:

```php
return [
    'yiisoft/yii-debug' => [
        'collectors' => [
            \App\Debug\AwesomeCollector::class,
        ],
        'trackedServices' => [
            // Decorating service => [Decorator, Collector],
            \Psr\Log\LoggerInterface::class => [\App\Debug\LoggerInterfaceProxy::class, \App\Debug\AwesomeCollector::class],
        ],
    ],
];
```
