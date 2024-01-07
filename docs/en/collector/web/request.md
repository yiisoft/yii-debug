# [Request collector](./../../../../src/Collector/Web/RequestCollector.php)

`RequestCollector` collects all middleware calls on the route to the action.

It uses [`\Yiisoft\Yii\Http\Event\BeforeRequest`](https://github.com/yiisoft/yii-http/blob/master/src/Event/BeforeRequest.php) and [`\Yiisoft\Yii\Http\Event\AfterRequest`](https://github.com/yiisoft/yii-http/blob/master/src/Event/AfterRequest.php) events to collect data.

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
    "requestUrl": "http:\/\/localhost:8080\/",
    "requestPath": "\/",
    "requestQuery": "",
    "requestMethod": "GET",
    "requestIsAjax": false,
    "userIp": "127.0.0.1",
    "responseStatusCode": 200,
    "request": "object@HttpSoft\\Message\\ServerRequest#8354",
    "requestRaw": "GET \/ HTTP\/1.1\r\nHost: localhost:8080\r\nSec-Fetch-Site: none\r\nCookie: PHPSESSID=gdell7ous8abq9fda0iif62v1bnhujgcqbdsp6sc2udlv98c\r\nConnection: keep-alive\r\nUpgrade-Insecure-Requests: 1\r\nSec-Fetch-Mode: navigate\r\nAccept: text\/html,application\/xhtml+xml,application\/xml;q=0.9,*\/*;q=0.8\r\nUser-Agent: Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\/605.1.15 (KHTML, like Gecko) Version\/17.1.2 Safari\/605.1.15\r\nAccept-Language: en-GB,en;q=0.9\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate\r\n\r\n",
    "response": "object@Yiisoft\\DataResponse\\DataResponse#7727",
    "responseRaw": "HTTP\/1.1 200 OK\r\nX-Debug-Id: 65994d62e94e3558828890\r\nX-Debug-Link: \/debug\/api\/view\/65994d62e94e3558828890\r\nContent-Type: text\/html; charset=UTF-8\r\n\r\n    <!DOCTYPE html>\n    <html class=\"h-100\" lang=\"en\">\n    <head>\n        <meta charset=\"utf-8\">\n        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n        <title>Yii Demo - Home<\/title>\n        <meta name=\"csrf\" content=\"J2_rzkynBZYrN30g_AoBFMOpwgDpvdJ85PMU2VwSphNvHpO6AJdRxlpRCGaGSGZC9OeBWt_FtTaWnEDrLlTiJQ==\">\n<meta name=\"generator\" content=\"Yii\">\n<link href=\"\/favicon.ico\" rel=\"icon\">\n<link href=\"\/assets\/9578d82c\/css\/toolbar.css\" rel=\"stylesheet\">\n<link href=\"\/assets\/7553f5c3\/css\/bootstrap.css\" rel=\"stylesheet\">\n<link href=\"https:\/\/cdn.jsdelivr.net\/npm\/bootstrap-icons@1.7.2\/font\/bootstrap-icons.css\" rel=\"stylesheet\">\n<link href=\"\/assets\/552b050\/css\/site.css\" rel=\"stylesheet\">    <\/head>\n    <body class=\"cover-container-fluid d-flex w-100 h-100 mx-auto flex-column\">\n    <header class=\"mb-auto\">\n        \n        <nav id=\"w1-navbar\" class=\"navbar navbar-light bg-light navbar-expand-sm text-white navbar-expand-lg\"><div class=\"container\"><a class=\"navbar-brand\" href=\"\/\">Yii Demo<\/a><button type=\"button\" class=\"navbar-toggler\" data-bs-toggle=\"collapse\" aria-label=\"Toggle navigation\" aria-controls=\"w2-collapse\" data-bs-target=\"#w2-collapse\" aria-expanded=\"false\"><span class=\"navbar-toggler-icon\"><\/span><\/button><div id=\"w2-collapse\" class=\"collapse navbar-collapse\">\n        <ul id=\"w3-nav\" class=\"navbar-nav mx-auto nav\"><li class=\"nav-item\"><a class=\"nav-link\" href=\"\/blog\">Blog<\/a><\/li>\n<li class=\"nav-item\"><a class=\"nav-link\" href=\"\/blog\/comments\">Comments Feed<\/a><\/li>\n<li class=\"nav-item\"><a class=\"nav-link\" href=\"\/user\">Users<\/a><\/li>\n<li class=\"nav-item\"><a class=\"nav-link\" href=\"\/contact\">Contact<\/a><\/li>\n<li class=\"nav-item\"><a class=\"nav-link\" href=\"\/docs\">Swagger<\/a><\/li><\/ul>\n        <ul id=\"w4-nav\" class=\"navbar-nav nav\"><li class=\"dropdown nav-item\"><a class=\"dropdown-toggle nav-link\" href=\"#\" data-bs-toggle=\"dropdown\">Language<\/a><ul id=\"w5-dropdown\" class=\"dropdown-menu\">\n<li><a class=\"dropdown-item active\" href=\"\/en\/\">English<\/a><\/li>\n<li><a class=\"dropdown-item\" href=\"\/ru\/\">Русский<\/a><\/li>\n<li><a class=\"dropdown-item\" href=\"\/sk\/\">Slovenský<\/a><\/li>\n<li><a class=\"dropdown-item\" href=\"\/id\/\">Indonesia<\/a><\/li>\n<li><a class=\"dropdown-item\" href=\"\/de\/\">German<\/a><\/li>\n<\/ul><\/li>\n<li class=\"nav-item\"><a class=\"nav-link\" href=\"\/login\">Login<\/a><\/li>\n<li class=\"nav-item\"><a class=\"nav-link\" href=\"\/signup\">Signup<\/a><\/li>\n<\/ul>        <\/div><\/div><\/nav>    <\/header>\n\n    <main class=\"container py-3\">\n        <div id=\"w0-carousel\" class=\"carousel slide\" data-bs-ride=\"carousel\"><ol class=\"carousel-indicators\"><li class=\"active\" data-bs-target=\"#w0-carousel\" data-bs-slide-to=\"0\"><\/li>\n<li data-bs-target=\"#w0-carousel\" data-bs-slide-to=\"1\"><\/li>\n<li data-bs-target=\"#w0-carousel\" data-bs-slide-to=\"2\"><\/li><\/ol><div class=\"carousel-inner\"><div class=\"carousel-item active\"><div class=\"d-block w-100 bg-info\" style=\"height: 200px\"><\/div>\n<div class=\"d-none d-md-block carousel-caption\"><h5>Hello, everyone!<\/h5><p>A great day to try Yii 3, right?<\/p><\/div><\/div>\n<div class=\"carousel-item\"><div class=\"d-block w-100 bg-secondary\" style=\"height: 200px\"><\/div>\n<div class=\"d-none d-md-block carousel-caption\"><h5>Code awaits!<\/h5><p>Check the project code. It's not ideal since it's a development sandbox as well, but gives a so-so overview of Yii 3 capabilities.<\/p><\/div><\/div>\n<div class=\"carousel-item\"><div class=\"d-block w-100 bg-dark\" style=\"height: 200px\"><\/div>\n<div class=\"d-none d-md-block carousel-caption\"><h5>We need feedback!<\/h5><p>Please leave your feedback in either Telegram or Slack mentioned in README.<\/p><\/div><\/div><\/div><a class=\"carousel-control-prev\" href=\"#w0-carousel\" data-bs-slide=\"prev\" role=\"button\"><span class=\"carousel-control-prev-icon\" aria-hidden=\"true\"><\/span><span class=\"visually-hidden\">Previous<\/span><\/a>\n<a class=\"carousel-control-next\" href=\"#w0-carousel\" data-bs-slide=\"next\" role=\"button\"><span class=\"carousel-control-next-icon\" aria-hidden=\"true\"><\/span><span class=\"visually-hidden\">Next<\/span><\/a><\/div>\n\n<div class=\"card mt-3 col-md-8\">\n    <div class=\"card-body\">\n        <h2 class=\"card-title\">Console<\/h2>\n                <h4 class=\"card-title text-muted\">Create new user<\/h4>\n        <div>\n            <code>.\/yii user\/create &lt;login&gt; &lt;password&gt; [isAdmin = 0]<\/code>\n        <\/div>\n        <h4 class=\"card-title text-muted mt-2 mb-1\">Assign RBAC role to user<\/h4>\n        <div>\n            <code>.\/yii user\/assignRole &lt;role&gt; &lt;userId&gt;<\/code>\n        <\/div>\n        <h4 class=\"card-title text-muted mt-2 mb-1\">Add random content<\/h4>\n        <div>\n            <code>.\/yii fixture\/add [count = 10]<\/code>\n        <\/div>\n        <h4 class=\"card-title text-muted mt-2 mb-1\">Migrations<\/h4>\n        <div>\n            <code>.\/yii migrate\/create<\/code>\n            <br><code>.\/yii migrate\/generate<\/code>\n            <br><code>.\/yii migrate\/up<\/code>\n            <br><code>.\/yii migrate\/down<\/code>\n            <br><code>.\/yii migrate\/list<\/code>\n        <\/div>\n        <h4 class=\"card-title text-muted mt-2 mb-1\">DB Schema<\/h4>\n        <div>\n            <code>.\/yii cycle\/schema<\/code>\n            <br><code>.\/yii cycle\/schema\/php<\/code>\n            <br><code>.\/yii cycle\/schema\/clear<\/code>\n            <br><code>.\/yii cycle\/schema\/rebuild<\/code>\n        <\/div>\n    <\/div>\n<\/div>\n    <\/main>\n\n    <footer class='mt-auto bg-dark py-3'>\n        <div class = 'd-flex flex-fill align-items-center container-fluid'>\n            <div class = 'd-flex flex-fill float-start'>\n                <i class=''><\/i>\n                <a class='text-decoration-none' href='https:\/\/www.yiiframework.com\/' target='_blank' rel='noopener'>\n                    Yii Framework - 2024 -\n                <\/a>\n                <div class=\"ms-2 text-white\">\n                    Time: 0.1788 s. Memory: 2.6849 mb.                <\/div>\n            <\/div>\n\n            <div class='float-end'>\n                <a class='text-decoration-none px-1' href='https:\/\/github.com\/yiisoft' target='_blank' rel='noopener' >\n                    <i class=\"bi bi-github text-white\"><\/i>\n                <\/a>\n                <a class='text-decoration-none px-1' href='https:\/\/join.slack.com\/t\/yii\/shared_invite\/enQtMzQ4MDExMDcyNTk2LTc0NDQ2ZTZhNjkzZDgwYjE4YjZlNGQxZjFmZDBjZTU3NjViMDE4ZTMxNDRkZjVlNmM1ZTA1ODVmZGUwY2U3NDA' target='_blank' rel='noopener'>\n                    <i class=\"bi bi-slack text-white\"><\/i>\n                <\/a>\n                <a class='text-decoration-none px-1' href='https:\/\/www.facebook.com\/groups\/yiitalk' target='_blank' rel='noopener'>\n                    <i class=\"bi bi-facebook text-white\"><\/i>\n                <\/a>\n                <a class='text-decoration-none px-1' href='https:\/\/twitter.com\/yiiframework' target='_blank' rel='noopener'>\n                    <i class=\"bi bi-twitter text-white\"><\/i>\n                <\/a>\n                <a class='text-decoration-none px-1' href='https:\/\/t.me\/yii3ru' target='_blank' rel='noopener'>\n                    <i class=\"bi bi-telegram text-white\"><\/i>\n                <\/a>\n            <\/div>\n        <\/div>\n    <\/footer>\n\n    <script src=\"\/assets\/9578d82c\/js\/toolbar.js\"><\/script>\n<script src=\"\/assets\/7553f5c3\/js\/bootstrap.bundle.js\"><\/script>\n<script src=\"\/assets\/552b050\/js\/app.js\"><\/script>\n<script>window.YiiDebug.initToolbar('\/debug', '\/debug\/api', 'phpstorm:\/\/open?url=file:\/\/{file}&line={line}')<\/script>    <\/body>\n    <\/html>\n"
},
```

### Summary

```json
{
    "request": {
        "url": "http:\/\/localhost:8080\/",
        "path": "\/",
        "query": "",
        "method": "GET",
        "isAjax": false,
        "userIp": "127.0.0.1"
    },
    "response": {
        "statusCode": 200
    }
}
```
