# Filtering

Disabling debugging for certain requests or console commands may help you to debug in production or not to flood the debug storage with useless payloads.

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

See [`\Yiisoft\Strings\WildcardPattern`](https://github.com/yiisoft/strings#wildcardpattern-usage) for more details about the pattern syntax.

In order to disable debugging certain console commands you can also specify them via `ignoredCommands`.
Here is default ignored command list:

```php
return [
    'yiisoft/yii-debug' => [
        'ignoredCommands' => [
            'completion',
            'help',
            'list',
            'serve',
            'debug:reset',
        ],
    ],
    // ...
];
```
