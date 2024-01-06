# Collectors

## [EventDispatcher collector](./../../../src/Collector/EventCollector.php)

`EventCollector` collects all events dispatched by [`\Psr\EventDispatcher\EventDispatcherInterface`](https://github.com/php-fig/event-dispatcher/blob/master/src/EventDispatcherInterface.php).

It uses [`\Yiisoft\Yii\Debug\Collector\EventDispatcherInterfaceProxy`](./../../../src/Collector/EventDispatcherInterfaceProxy.php) proxy to wrap the original PSR-14 event dispatcher and proxy all calls to the collector.

## Collected data

### Common

Example:

```php
final class SiteController
{
    public function __construct(private ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withController($this);
    }

    public function index(EventDispatcherInterface $dispatcher): ResponseInterface
    {
        $dispatcher->dispatch(new \stdClass());
        return $this->viewRenderer->render('index');
    }
}

```

Output:

```json
[
    {
        "name": "stdClass",
        "event": "object@stdClass#7742",
        "file": false,
        "line": ".../demo\/blog\/src\/Controller\/SiteController.php:20",
        "time": 1704545249.06457
    }
]
```

### Summary

```json
{
    "total": 1
}
```
