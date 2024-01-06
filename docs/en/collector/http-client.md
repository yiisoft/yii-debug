# Collectors

## [HttpClient collector](./../../../src/Collector/HttpClientCollector.php)

`HttpClientCollector` collects all requests sent by [`Psr\Http\Client\ClientInterface`](https://github.com/php-fig/http-client/blob/master/src/ClientInterface.php).

It uses [`\Yiisoft\Yii\Debug\Collector\HttpClientInterfaceProxy`](./../../../src/Collector/HttpClientInterfaceProxy.php) proxy to wrap the original PSR-18 client and proxy all calls to the collector.

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
        "startTime": 1704545634.973538,
        "endTime": 1704545635.120111,
        "totalTime": 0.14657306671142578,
        "method": "GET",
        "uri": "https:\/\/google.com",
        "headers": {
            "Host": [
                "google.com"
            ]
        },
        "line": ".../demo\/blog\/src\/Controller\/SiteController.php:22",
        "responseRaw": "HTTP\/1.1 301 Moved Permanently\r\nLocation: https:\/\/www.google.com\/\r\nContent-Type: text\/html; charset=UTF-8\r\nContent-Security-Policy-Report-Only: object-src 'none';base-uri 'self';script-src 'nonce-1jfBaOK8wM3oVDi7ClviDg' 'strict-dynamic' 'report-sample' 'unsafe-eval' 'unsafe-inline' https: http:;report-uri https:\/\/csp.withgoogle.com\/csp\/gws\/other-hp\r\nDate: Sat, 06 Jan 2024 12:53:55 GMT\r\nExpires: Mon, 05 Feb 2024 12:53:55 GMT\r\nCache-Control: public, max-age=2592000\r\nServer: gws\r\nContent-Length: 220\r\nX-XSS-Protection: 0\r\nX-Frame-Options: SAMEORIGIN\r\nAlt-Svc: h3=\":443\"; ma=2592000,h3-29=\":443\"; ma=2592000\r\n\r\n<HTML><HEAD><meta http-equiv=\"content-type\" content=\"text\/html;charset=utf-8\">\n<TITLE>301 Moved<\/TITLE><\/HEAD><BODY>\n<H1>301 Moved<\/H1>\nThe document has moved\n<A HREF=\"https:\/\/www.google.com\/\">here<\/A>.\r\n<\/BODY><\/HTML>\r\n",
        "responseStatus": 301
    }
]
```

### Summary

```json
{
    "count": 1,
    "totalTime": 0.14657306671142578
}
```
