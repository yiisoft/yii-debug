# [Log collector](./../../../src/Collector/LogCollector.php)

`LogCollector` collects all data logged by [`\Psr\Log\LoggerInterface`](https://github.com/php-fig/log/blob/master/src/LoggerInterface.php).

It uses [`\Yiisoft\Yii\Debug\Collector\LoggerInterfaceProxy`](./../../../src/Collector/LoggerInterfaceProxy.php) proxy to wrap the original PSR-3 logger and proxy all calls to the collector.

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

    public function index(LoggerInterface $logger): ResponseInterface
    {
        $logger->debug('Hello, world!', ['category' => 'debug']);
        $logger->info('Hello, world!', ['category' => 'info']);
        return $this->viewRenderer->render('index');
    }
}

```

Output:

```json
[
    {
        "time": 1704544908.712395,
        "level": "debug",
        "message": "Hello, world!",
        "context": {
            "category": "debug"
        },
        "line": ".../demo\/blog\/src\/Controller\/SiteController.php:21"
    },
    {
        "time": 1704544908.712417,
        "level": "info",
        "message": "Hello, world!",
        "context": {
            "category": "info"
        },
        "line": ".../demo\/blog\/src\/Controller\/SiteController.php:22"
    }
]
```

### Summary

```json
{
    "total": 2
}
```
