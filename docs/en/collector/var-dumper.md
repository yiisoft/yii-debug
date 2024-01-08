# [VarDumper collector](./../../../src/Collector/VarDumperCollector.php)

`VarDumperCollector` collects all data dumped
by [`\Yiisoft\Yii\Debug\VarDumper\VarDumper`](https://github.com/yiisoft/var-dumper/blob/master/src/VarDumper.php) or
its shortcut functions `dump()`, `d()`, and `dd()`.

It uses [`\Yiisoft\Yii\Debug\Collector\VarDumperHandlerInterfaceProxy`](./../../../src/Collector/VarDumperHandlerInterfaceProxy.php) proxy to wrap the original VarDumper's [`HandlerInterface`](https://github.com/yiisoft/var-dumper/blob/master/src/HandlerInterface.php) and proxy all calls to the collector.

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

    public function index(): ResponseInterface
    {
        d(['test'], 1, new stdClass());
        return $this->viewRenderer->render('index');
    }
}
```

Output:

```json
[
    {
        "variable": [
            "test"
        ],
        "line": ".../demo\/blog\/src\/Controller\/SiteController.php:20"
    },
    {
        "variable": 1,
        "line": ".../demo\/blog\/src\/Controller\/SiteController.php:20"
    },
    {
        "variable": "object@stdClass#7735",
        "line": ".../demo\/blog\/src\/Controller\/SiteController.php:20"
    }
]
```

### Summary

```json
{
    "var-dumper": {
        "total": 3
    }
}
```
