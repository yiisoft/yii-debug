# [Exception collector](./../../../src/Collector/ExceptionCollector.php)

`ExceptionCollector` collects all data about an exception that was not caught by the application.

It uses [`\Yiisoft\ErrorHandler\Event\ApplicationError`](https://github.com/yiisoft/error-handler/blob/master/src/Event/ApplicationError.php) event to collect data.

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
        throw new \Exception('Hello, world!');
        return $this->viewRenderer->render('index');
    }
}

```

Output:

```json
[
    {
        "class": "Exception",
        "message": "Hello, world!",
        "file": ".../demo\/blog\/src\/Controller\/SiteController.php",
        "line": 19,
        "code": 0,
        "trace": [
            {
                "function": "index",
                "class": "App\\Controller\\SiteController",
                "type": "->",
                "args": [
                    "object@HttpSoft\\Message\\ServerRequest#8094",
                    "object@App\\Handler\\NotFoundHandler#8503"
                ]
            },
            // long long stack trace
            // ...
            {
                "file": ".../demo\/blog\/vendor\/yiisoft\/yii-runner-http\/src\/HttpApplicationRunner.php",
                "line": 144,
                "function": "handle",
                "class": "Yiisoft\\Yii\\Http\\Application",
                "type": "->",
                "args": [
                    "object@HttpSoft\\Message\\ServerRequest#8354"
                ]
            },
            {
                "file": ".../demo\/blog\/public\/index.php",
                "line": 40,
                "function": "run",
                "class": "Yiisoft\\Yii\\Runner\\Http\\HttpApplicationRunner",
                "type": "->",
                "args": []
            }
        ],
        "traceAsString": "..." // same long stack trace, but as a string
    }
]
```

### Summary

```json
{
    "exception": {
        "class": "Exception",
        "message": "Hello, world!",
        "file": ".../demo\/blog\/src\/Controller\/SiteController.php",
        "line": 19,
        "code": 0
    }
}
```
