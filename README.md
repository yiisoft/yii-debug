<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Debug Extension for Yii</h1>
    <br>
</p>

This extension provides a debugger for [Yii framework](https://www.yiiframework.com) applications. When this extension is used,
a debugger toolbar will appear at the bottom of every page. The extension also provides
a set of standalone pages to display more detailed debug information.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii-debug/v/stable.png)](https://packagist.org/packages/yiisoft/yii-debug)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii-debug/downloads.png)](https://packagist.org/packages/yiisoft/yii-debug)
[![Build status](https://github.com/yiisoft/yii-debug/workflows/build/badge.svg)](https://github.com/yiisoft/yii-debug/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/yii-debug/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-debug/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/yii-debug/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/yii-debug/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fyii-debug%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/yii-debug/master)
[![static analysis](https://github.com/yiisoft/yii-debug/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/yii-debug/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/yii-debug/coverage.svg)](https://shepherd.dev/github/yiisoft/yii-debug)

Installation
------------

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

```
composer require yiisoft/yii-debug --dev
```

> The debug extension also can be installed without the `--dev` flag if you want to collect data in production.
> Specify needed collectors only to reduce functions overriding and improve performance.

Usage
-----

Once the extension is installed, modify your `config/common/params.php` as follows:

```php
return [
    'yiisoft/yii-debug' => [
        'enabled' => true,
    ],
    ...
];
```

All included collectors start listen and collect payloads from each HTTP request or console run.

Install both [`yiisoft/yii-debug-api`](https://github.com/yiisoft/yii-debug-api) and [`yiisoft/yii-dev-panel`](https://github.com/yiisoft/yii-dev-panel)
to be able to interact with collected data through UI.

## Collectors

Yii Debug uses a concept named "collectors". 
Each collector decides what payload it needs to collect and exports the collected payload in order to save it into storage.

A collector may work either both with HTTP requests and console runs, or individually.
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

Use the trait to reduce the duplication of code and any possible bugs: [`\Yiisoft\Yii\Debug\Collector\CollectorTrait`](./src/Collector/CollectorTrait.php)

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

When you implement collecting payload, it is also a good idea to implement data reset. With `CollectorTrait` it is as simple as adding `reset` method:
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

#### Summary collector

Summary collector is a collector that provides additional "summary" payload. 
The summary payload is used to reduce time to read usual payload and summarise some metrics to get better UX.

Summary collector is usual collector with the additional method `getSummary()`. 
Take a look at the [`\Yiisoft\Yii\Debug\Collector\SummaryCollectorInterface`](./src/Collector/SummaryCollectorInterface.php):

```php
namespace Yiisoft\Yii\Debug\Collector;

/**
 * Summary data collector responsibility is to collect summary data for a collector.
 * Summary is used to display a list of previous requests and select one to display full info.
 * Its data set is specific to the list and is reduced compared to full data collected
 * in {@see CollectorInterface}.
 */
interface SummaryCollectorInterface extends CollectorInterface
{
    /**
     * @return array Summary payload. Keys may cross with any other summary collectors.
     */
    public function getSummary(): array;
}
```

We suggest you to give short names to your summary payload to be able to read the keys and decide to use them or not.

```php
    // with getCollected you can inspect all collected payload
    public function getCollected(): array
    {
        return $this->requests;
    }

    // getSummary gives you short description of the collected data just to decide inspect it deeper or not
    public function getSummary(): array
    {
        return [
            'web' => [
                'totalRequests' => count($this->requests),
            ],
        ];
    }
```

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/infection
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

### Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)

## License

The Debug Extension for Yii is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
