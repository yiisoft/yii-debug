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
> Just specify needed collectors to reduce functions overriding.

Usage
-----

Once the extension is installed, simply modify your application `params` as follows:

`config/common/params.php`:
```php
return [
    'yiisoft/yii-debug' => [
        'enabled' => true,
    ],
    // ...
];
```

All included collectors start listen and collect payloads from each HTTP request or console running.

Install both [`yiisoft/yii-debug-api`](https://github.com/yiisoft/yii-debug-api) and [`yiisoft/yii-dev-panel`](https://github.com/yiisoft/yii-dev-panel)
to be able to interact with collected data through UI.

## Logging

Specify the filesystem path where collected data will be stored if you use `FileStorage` by adding the lines into the configuration:

```php
return [
    'yiisoft/yii-debug' => [
        // It's default path to store collected payload
        // @runtime = @root/runtime
        'path' => '@runtime/debug',
    ],
    // ...
];
```

## Filtering

Disabling debugging may help you to debug in production or not to flood to the debug storage with payload which never be used for debug purpose.

You can specify which routes should not trigger the Debug extension by adding the ones into the configuration:

```php
return [
    'yiisoft/yii-debug' => [
        'ignoredRequests' => [
            '/assets/**',
        ],
    ],
    // ...
];
```

It uses regular expressions under the hood, but looks simpler for users. See (`\Yiisoft\Strings\WildcardPattern`)[https://github.com/yiisoft/strings#wildcardpattern-usage] for more details.

In order to disable debugging console commands you can also specify them into another directive `ignoredCommands`.
Here is default ignored command list:

```php
return [
    'yiisoft/yii-debug' => [
        'ignoredCommands' => [
            'completion',
            'help',
            'list',
            'serve',
            'debug/reset',
        ],
    ],
    // ...
];
```

## Collectors

Yii Debug uses a concept named "collectors". 
Each collector decides what payload it needs to collect and exports the collected payload in order to save it into storage.

A collector may work both with HTTP request and console running, or individually.
A collector may be as just a event listener as a decorator to any service from application dependency injection container configuration.

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

We suggest to use the trait to reduce the duplication of code and any possible bugs: [`\Yiisoft\Yii\Debug\Collector\CollectorTrait`](./src/Collector/CollectorTrait.php)

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

Good way to implement collecting payload is also implement a resetting the data. With `CollectorTrait` it is simple and you just need to add `reset` method:
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
        // if you want to register collector both for web session and console run
        'collectors' => [
            \App\Debug\AwesomeCollector::class,
        ],
        // if you want to register collector only for web session
        'collectors.web' => [
            \App\Debug\AwesomeWebCollector::class,
        ],
        // if you want to register collector only for console run
        'collectors.console' => [
            \App\Debug\AwesomeConsoleCollector::class,
        ],
    ],
];
```

Under `yiisoft/yii-debug` configuration you may use:
1. `collectors` key for both web and console runs
2. `collectors.web` key only for web session
3. `collectors.console` key only for console run

> Do not register a collector for a session where the collector will not collect any payload.


The lines above connect collectors with a debug extension run. 
Under the hood it just calls `getCollected()` method from the collectors at the end of application cycle run.

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

Proxy collectors are used in case you want to decorate a service from DIC and sniff methods' calls with its values.

First you need to create a class that will work as a decorator. See https://en.wikipedia.org/wiki/Decorator_pattern if you are new with it.

Decorators may inject any services through `__construct` method, but you should specify services you like to wrap.
Into section `trackedServices` of `yiisoft/yii-debug` configuration you should specify:
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

## Index collector

Index collector is a collector that provides additional "summary" payload. 
The summary payload is used to reduce time to read usual payload and summarise some metrics to get better UX.

Index collector is usual collector with the additional method `getIndexData()`. 
Take a look at the [`\Yiisoft\Yii\Debug\Collector\IndexCollectorInterface`](./src/Collector/IndexCollectorInterface.php):

```php
namespace Yiisoft\Yii\Debug\Collector;

/**
 * Index data collector responsibility is to collect index data during application lifecycle.
 * Index is used to display a list of previous requests and select one to display full info.
 * Its data set is specific to the list and is reduced compared to full data collected
 * in {@see \Yiisoft\Yii\Debug\Collector\CollectorInterface}.
 */
interface IndexCollectorInterface extends CollectorInterface
{
    /**
     * @return array data indexed
     */
    public function getIndexData(): array;
}
```

We suggest you to give a short name to your index payload to be able to grab it later.
```php
    // with getCollected you can inspect all collected payload
    public function getCollected(): array
    {
        return $this->requests;
    }

    // getIndexData gives you short description of the collected data just to decide inspect it deeper or not
    public function getIndexData(): array
    {
        return [
            'app' => [
                'totalRequests' => count($this->requests),
            ],
        ];
    }
```

## ServiceCollector

ServiceCollector is a collector that listen all tracked services and collect its arguments, results and errors.

By default, the debug extension has enabled [`\Yiisoft\Yii\Debug\Collector\ServiceCollector`](./src/Collector/ServiceCollector.php) with the following settings:
1. Log arguments
2. Log results
3. Log errors

It may worse the application performance in development what why we recommend to disable this mechanism in production.

You may control the things are being logged by specifying the settings in the configuration:

```php
use Yiisoft\Yii\Debug\Collector\ContainerInterfaceProxy;

return [
    'yiisoft/yii-debug' => [
        // use 0 or ContainerInterfaceProxy::LOG_NOTHING to disable logging
        'logLevel' => ContainerInterfaceProxy::LOG_ARGUMENTS | ContainerInterfaceProxy::LOG_RESULT | ContainerInterfaceProxy::LOG_ERROR,
    ],
];
```

## Console commands

### `debug/reset`

The command clean all collected data. It's similar to `rm -rf runtime/debug` if you use file storage, but may be also useful if you use another storage driver.

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
