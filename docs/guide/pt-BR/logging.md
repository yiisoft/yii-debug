# Logging

If you use `FileStorage`, specify the filesystem path where collected data will be stored by adding the following lines into the configuration:

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

## Driver

You can specify the storage driver by adding the following lines into the `di` configuration:

```php
return [
    \Yiisoft\Yii\Debug\Storage\StorageInterface::class => \Yiisoft\Yii\Debug\Storage\FileStorage::class,
];
```

> Note: Currently, only `FileStorage` and `MemoryStorage` are supported.
