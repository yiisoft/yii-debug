<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

return (new Configuration())
    ->disableComposerAutoloadPathScan()
    ->setFileExtensions(['php'])
    ->addPathToScan(__DIR__ . '/config', isDev: false)
    ->addPathToScan(__DIR__ . '/src', isDev: false)
    ->addPathToScan(__DIR__ . '/tests', isDev: true)
    // Optional integration: gracefully degrades with a clear LogicException when yiisoft/aliases isn't installed
    // (config/di.php).
    ->ignoreUnknownClasses(['Yiisoft\Aliases\Aliases'])
    // Optional integration, guarded by function_exists() (src/Helper/StreamWrapper/StreamWrapper.php).
    ->ignoreErrorsOnExtension('ext-zend-opcache', [ErrorType::SHADOW_DEPENDENCY])
    // Referenced only via ::class constants in an "excludedClasses" config list (config/params.php), never
    // instantiated; nikic/php-parser is a transitive dev-tooling dependency, not a real runtime need.
    ->ignoreErrorsOnPackages(['nikic/php-parser'], [ErrorType::SHADOW_DEPENDENCY])
    // Optional integrations tracked via "trackedServices" config (config/params.php): only exercised when the
    // consuming application already has these packages installed for its own purposes.
    ->ignoreErrorsOnPackages(
        ['yiisoft/error-handler', 'yiisoft/injector', 'yiisoft/yii-console', 'yiisoft/yii-http'],
        [ErrorType::DEV_DEPENDENCY_IN_PROD],
    )
    // `yiisoft/definitions` is used only in `config/di-web.php` and `config/di-console.php`, which are loaded
    // by consumers using `yiisoft/di`, that already requires `yiisoft/definitions` itself.
    ->ignoreErrorsOnPackages(['yiisoft/definitions'], [ErrorType::SHADOW_DEPENDENCY]);
