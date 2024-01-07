# [WebAppInfo collector](./../../../../src/Collector/Web/WebAppInfoCollector.php)

`WebAppInfoCollector` collects all middleware calls on the route to the action.

It uses the following events to collect data: 
- [`\Yiisoft\Yii\Http\Event\ApplicationStartup`](https://github.com/yiisoft/yii-http/blob/master/src/Event/ApplicationStartup.php)
- [`\Yiisoft\Yii\Http\Event\ApplicationShutdown`](https://github.com/yiisoft/yii-http/blob/master/src/Event/ApplicationShutdown.php)
- [`\Yiisoft\Yii\Http\Event\BeforeRequest`](https://github.com/yiisoft/yii-http/blob/master/src/Event/BeforeRequest.php)
- [`\Yiisoft\Yii\Http\Event\AfterRequest`](https://github.com/yiisoft/yii-http/blob/master/src/Event/AfterRequest.php)
- [`\Yiisoft\Yii\Http\Event\AfterEmit`](https://github.com/yiisoft/yii-http/blob/master/src/Event/AfterEmit.php)

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
        return $this->viewRenderer->render('index');
    }
}
```

Output:

```json
{
    "applicationProcessingTime": 1704545635.136016,
    "requestProcessingTime": 0.17957496643066406,
    "applicationEmit": 0.00048089027404785156,
    "preloadTime": 1704545634.95596,
    "memoryPeakUsage": 10567480,
    "memoryUsage": 2170160
}
```

### Summary

```json
{
    "web": {
        "php": {
            "version": "8.2.8"
        },
        "request": {
            "startTime": 1704545634.95596,
            "processingTime": 0.17957496643066406
        },
        "memory": {
            "peakUsage": 10567480
        }
    }
}
```
