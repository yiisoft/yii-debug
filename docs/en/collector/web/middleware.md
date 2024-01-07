# [Middleware collector](./../../../../src/Collector/Web/MiddlewareCollector.php)

`MiddlewareCollector` collects all middleware calls on the route to the action.

It uses [`\Yiisoft\Middleware\Dispatcher\Event\BeforeMiddleware`](https://github.com/yiisoft/middleware-dispatcher/blob/master/src/Event/BeforeMiddleware.php) and [`\Yiisoft\Middleware\Dispatcher\Event\AfterMiddleware`](https://github.com/yiisoft/middleware-dispatcher/blob/master/src/Event/AfterMiddleware.php) events to collect data.

## Collected data

### Common

Example:

`common/routes/routes.php`

```php
return [
    Route::get('/')
        ->action([SiteController::class, 'index'])
        ->name('site/index'),
];
```

`common/di/router.php`
```php
return [
    RouteCollectionInterface::class => static function (RouteCollectorInterface $collector) use ($config) {
        $collector
            ->middleware(CsrfMiddleware::class)
            ->middleware(FormatDataResponse::class)
            ->addGroup(
                Group::create('/{_language}')->routes(...$config->get('app-routes')),
            )
            ->addGroup(
                Group::create()->routes(...$config->get('routes')),
            );

        if (!str_starts_with(getenv('YII_ENV') ?: '', 'prod')) {
            $collector->middleware(ToolbarMiddleware::class);
        }

        return new RouteCollection($collector);
    },
];
```

`web/params.php`
```php
return [
    'middlewares' => [
        ErrorCatcher::class,
        SentryMiddleware::class,
        SessionMiddleware::class,
        CookieMiddleware::class,
        CookieLoginMiddleware::class,
        Subfolder::class,
        Locale::class,
        Router::class,
    ],
];
```

`SiteController.php`

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
    "beforeStack": [
        {
            "name": "Yiisoft\\ErrorHandler\\Middleware\\ErrorCatcher",
            "time": 1704545634.956736,
            "memory": 1610600,
            "request": "object@HttpSoft\\Message\\ServerRequest#8354"
        },
        {
            "name": "Yiisoft\\Yii\\Sentry\\SentryMiddleware",
            "time": 1704545634.957129,
            "memory": 1615728,
            "request": "object@HttpSoft\\Message\\ServerRequest#8354"
        },
        {
            "name": "Yiisoft\\Session\\SessionMiddleware",
            "time": 1704545634.957509,
            "memory": 1620512,
            "request": "object@HttpSoft\\Message\\ServerRequest#8354"
        },
        {
            "name": "Yiisoft\\Cookies\\CookieMiddleware",
            "time": 1704545634.958201,
            "memory": 1628592,
            "request": "object@HttpSoft\\Message\\ServerRequest#8354"
        },
        {
            "name": "Yiisoft\\User\\Login\\Cookie\\CookieLoginMiddleware",
            "time": 1704545634.958807,
            "memory": 1638080,
            "request": "object@HttpSoft\\Message\\ServerRequest#8340"
        },
        {
            "name": "Yiisoft\\Yii\\Middleware\\Subfolder",
            "time": 1704545634.959437,
            "memory": 1646472,
            "request": "object@HttpSoft\\Message\\ServerRequest#8340"
        },
        {
            "name": "Yiisoft\\Yii\\Middleware\\Locale",
            "time": 1704545634.9603,
            "memory": 1682408,
            "request": "object@HttpSoft\\Message\\ServerRequest#8340"
        },
        {
            "name": "Yiisoft\\Router\\Middleware\\Router",
            "time": 1704545634.961835,
            "memory": 1733592,
            "request": "object@HttpSoft\\Message\\ServerRequest#8099"
        },
        {
            "name": "Yiisoft\\Yii\\Debug\\Api\\Debug\\Middleware\\DebugHeaders",
            "time": 1704545634.970165,
            "memory": 1943640,
            "request": "object@HttpSoft\\Message\\ServerRequest#8099"
        },
        {
            "name": "Yiisoft\\Csrf\\CsrfMiddleware",
            "time": 1704545634.970354,
            "memory": 1946760,
            "request": "object@HttpSoft\\Message\\ServerRequest#8099"
        },
        {
            "name": "Yiisoft\\DataResponse\\Middleware\\FormatDataResponse",
            "time": 1704545634.970899,
            "memory": 1954032,
            "request": "object@HttpSoft\\Message\\ServerRequest#8099"
        },
        {
            "name": "Yiisoft\\Yii\\Debug\\Viewer\\Middleware\\ToolbarMiddleware",
            "time": 1704545634.971337,
            "memory": 1960632,
            "request": "object@HttpSoft\\Message\\ServerRequest#8099"
        }
    ],
    "actionHandler": {
        "name": "App\\Controller\\SiteController::index",
        "startTime": 1704545634.972557,
        "request": "object@HttpSoft\\Message\\ServerRequest#8099",
        "response": "object@Yiisoft\\DataResponse\\DataResponse#7742",
        "endTime": 1704545635.121126,
        "memory": 2003376
    },
    "afterStack": [
        {
            "name": "Yiisoft\\Yii\\Debug\\Viewer\\Middleware\\ToolbarMiddleware",
            "time": 1704545635.121346,
            "memory": 2004824,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7742"
        },
        {
            "name": "Yiisoft\\DataResponse\\Middleware\\FormatDataResponse",
            "time": 1704545635.121556,
            "memory": 2006752,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7739"
        },
        {
            "name": "Yiisoft\\Csrf\\CsrfMiddleware",
            "time": 1704545635.121755,
            "memory": 2008200,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7739"
        },
        {
            "name": "Yiisoft\\Yii\\Debug\\Api\\Debug\\Middleware\\DebugHeaders",
            "time": 1704545635.122048,
            "memory": 2011296,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\Router\\Middleware\\Router",
            "time": 1704545635.122247,
            "memory": 2012520,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\Yii\\Middleware\\Locale",
            "time": 1704545635.122442,
            "memory": 2013968,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\Yii\\Middleware\\Subfolder",
            "time": 1704545635.122647,
            "memory": 2015416,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\User\\Login\\Cookie\\CookieLoginMiddleware",
            "time": 1704545635.122843,
            "memory": 2016864,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\Cookies\\CookieMiddleware",
            "time": 1704545635.134779,
            "memory": 2159744,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\Session\\SessionMiddleware",
            "time": 1704545635.135053,
            "memory": 2161072,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\Yii\\Sentry\\SentryMiddleware",
            "time": 1704545635.135233,
            "memory": 2162520,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        },
        {
            "name": "Yiisoft\\ErrorHandler\\Middleware\\ErrorCatcher",
            "time": 1704545635.135413,
            "memory": 2163968,
            "response": "object@Yiisoft\\DataResponse\\DataResponse#7727"
        }
    ]
}
```

### Summary

```json
{
    "total": 12
}
```
