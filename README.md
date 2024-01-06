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
    // ...
];
```

All included collectors start listen and collect payloads from each HTTP request or console run.

Install both [`yiisoft/yii-debug-api`](https://github.com/yiisoft/yii-debug-api) and [`yiisoft/yii-dev-panel`](https://github.com/yiisoft/yii-dev-panel)
to be able to interact with collected data through UI.

### Documentation

[English](docs/en/index.md)

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
