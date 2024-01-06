# Console commands

## `debug:container`

The `debug/container` command displays all registered services and config groups.

For example:

```
❯ ./yii debug:container
+---------------------- params-console -----------------------+
| Group                        | Services                     |
+------------------------------+------------------------------+
| yiisoft/yii-console          | yiisoft/yii-console          |
| yiisoft/yii-event            | yiisoft/yii-event            |
| yiisoft/cache-file           | yiisoft/cache-file           |
| yiisoft/mailer               | yiisoft/mailer               |
| symfony/mailer               | symfony/mailer               |
| yiisoft/rbac-rules-container | yiisoft/rbac-rules-container |
| yiisoft/router-fastroute     | yiisoft/router-fastroute     |
| yiisoft/user                 | yiisoft/user                 |
| yiisoft/yii-cycle            | yiisoft/yii-cycle            |
| yiisoft/yii-dataview         | yiisoft/yii-dataview         |
| yiisoft/yii-debug            | yiisoft/yii-debug            |
| yiisoft/yii-debug-api        | yiisoft/yii-debug-api        |
| yiisoft/yii-swagger          | yiisoft/yii-swagger          |
| yiisoft/yii-sentry           | yiisoft/yii-sentry           |
| yiisoft/yii-debug-viewer     | yiisoft/yii-debug-viewer     |
| yiisoft/yii-gii              | yiisoft/yii-gii              |
| yiisoft/assets               | yiisoft/assets               |
| yiisoft/form                 | yiisoft/form                 |
| yiisoft/log-target-file      | yiisoft/log-target-file      |
| yiisoft/profiler             | yiisoft/profiler             |
| yiisoft/yii-view             | yiisoft/yii-view             |
| yiisoft/aliases              | yiisoft/aliases              |
| yiisoft/csrf                 | yiisoft/csrf                 |
| yiisoft/session              | yiisoft/session              |
| yiisoft/data-response        | yiisoft/data-response        |
| yiisoft/widget               | yiisoft/widget               |
| yiisoft/validator            | yiisoft/validator            |
| yiisoft/view                 | yiisoft/view                 |
| yiisoft/translator           | yiisoft/translator           |
| mailer                       | mailer                       |
| yiisoft/cookies              | yiisoft/cookies              |
+------------------------------+------------------------------+
+- bootstrap-c... -+
| Group | Services |
+-------+----------+
| 0     | Closure  |
| 1     | Closure  |
| 2     | Closure  |
| 3     | Closure  |
+-------+----------+
+-------------------------------------------------------------------------------- di-console ---------------------------------------------------------------------------------+
| Group                                                                                | Services                                                                             |
+--------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------+
| Cycle\Migrations\Migrator                                                            | Yiisoft\Yii\Cycle\Factory\MigratorFactory                                            |
| Cycle\Migrations\Config\MigrationConfig                                              | Yiisoft\Yii\Cycle\Factory\MigrationConfigFactory                                     |
| Yiisoft\Yii\Cycle\Command\CycleDependencyProxy                                       | Closure                                                                              |
| Yiisoft\TranslatorExtractor\Extractor                                                | Yiisoft\TranslatorExtractor\Extractor                                                |
| Symfony\Component\Console\CommandLoader\CommandLoaderInterface                       | Yiisoft\Yii\Console\CommandLoader                                                    |
| Yiisoft\Yii\Console\Command\Serve                                                    | Yiisoft\Yii\Console\Command\Serve                                                    |
| Yiisoft\Yii\Console\Application                                                      | Yiisoft\Yii\Console\Application                                                      |
| Yiisoft\Yii\Debug\Debugger                                                           | Yiisoft\Yii\Debug\Debugger                                                           |
| Yiisoft\EventDispatcher\Provider\ListenerCollection                                  | Closure                                                                              |
| Yiisoft\Cache\File\FileCache                                                         | Closure                                                                              |
| Yiisoft\FormModel\FormHydrator                                                       | Yiisoft\FormModel\FormHydrator                                                       |
| Yiisoft\Mailer\MessageBodyRenderer                                                   | Yiisoft\Mailer\MessageBodyRenderer                                                   |
| Yiisoft\Mailer\MessageFactoryInterface                                               | Yiisoft\Mailer\MessageFactory                                                        |
| Symfony\Component\Mailer\Transport\TransportInterface                                | Closure                                                                              |
| Yiisoft\Mailer\FileMailer                                                            | Yiisoft\Mailer\FileMailer                                                            |
| Yiisoft\Mailer\MailerInterface                                                       | Yiisoft\Mailer\FileMailer                                                            |
| Yiisoft\Rbac\RuleFactoryInterface                                                    | Yiisoft\Rbac\Rules\Container\RulesContainer                                          |
| Yiisoft\Router\UrlGeneratorInterface                                                 | Yiisoft\Router\FastRoute\UrlGenerator                                                |
| Cycle\Database\DatabaseProviderInterface                                             | Yiisoft\Definitions\Reference                                                        |
| Cycle\Database\DatabaseManager                                                       | Yiisoft\Yii\Cycle\Factory\DbalFactory                                                |
| Cycle\ORM\ORMInterface                                                               | Yiisoft\Definitions\Reference                                                        |
| Cycle\ORM\ORM                                                                        | Closure                                                                              |
| Cycle\ORM\EntityManagerInterface                                                     | Yiisoft\Definitions\Reference                                                        |
| Cycle\ORM\EntityManager                                                              | Cycle\ORM\EntityManager                                                              |
| Spiral\Core\FactoryInterface                                                         | Yiisoft\Definitions\Reference                                                        |
| Cycle\ORM\FactoryInterface                                                           | Closure                                                                              |
| Cycle\ORM\SchemaInterface                                                            | Closure                                                                              |
| Yiisoft\Yii\Cycle\Schema\SchemaProviderInterface                                     | Closure                                                                              |
| Yiisoft\Yii\Cycle\Schema\SchemaConveyorInterface                                     | Closure                                                                              |
| yii.dataview.categorySource                                                          | yii.dataview.categorySource                                                          |
| Sentry\Transport\TransportFactoryInterface                                           | Sentry\Transport\DefaultTransportFactory                                             |
| Sentry\HttpClient\HttpClientFactoryInterface                                         | Sentry\HttpClient\HttpClientFactory                                                  |
| Sentry\Options                                                                       | Sentry\Options                                                                       |
| Sentry\State\HubInterface                                                            | Sentry\State\Hub                                                                     |
| Yiisoft\Yii\Gii\GiiInterface                                                         | Closure                                                                              |
| Yiisoft\Yii\Gii\ParametersProvider                                                   | Yiisoft\Yii\Gii\ParametersProvider                                                   |
| Yiisoft\Log\Target\File\FileRotatorInterface                                         | Yiisoft\Log\Target\File\FileRotator                                                  |
| Yiisoft\Log\Target\File\FileTarget                                                   | Closure                                                                              |
| Yiisoft\Yii\Debug\Collector\ContainerProxyConfig                                     | Closure                                                                              |
| Yiisoft\Yii\Debug\Collector\Stream\FilesystemStreamCollector                         | Yiisoft\Yii\Debug\Collector\Stream\FilesystemStreamCollector                         |
| Yiisoft\Yii\Debug\Storage\StorageInterface                                           | Closure                                                                              |
| Yiisoft\Profiler\ProfilerInterface                                                   | Closure                                                                              |
| Yiisoft\Profiler\Target\LogTarget                                                    | Yiisoft\Profiler\Target\LogTarget                                                    |
| Yiisoft\Profiler\Target\FileTarget                                                   | Yiisoft\Profiler\Target\FileTarget                                                   |
| Yiisoft\Aliases\Aliases                                                              | Yiisoft\Aliases\Aliases                                                              |
| Yiisoft\Cache\CacheInterface                                                         | Yiisoft\Cache\Cache                                                                  |
| Psr\SimpleCache\CacheInterface                                                       | Yiisoft\Cache\File\FileCache                                                         |
| Yiisoft\Hydrator\HydratorInterface                                                   | Yiisoft\Hydrator\Hydrator                                                            |
| Yiisoft\Validator\ValidatorInterface                                                 | Yiisoft\Validator\Validator                                                          |
| Yiisoft\Validator\RuleHandlerResolverInterface                                       | Yiisoft\Validator\RuleHandlerResolver\RuleHandlerContainer                           |
| yii.validator.categorySource                                                         | yii.validator.categorySource                                                         |
| Yiisoft\View\View                                                                    | Yiisoft\View\View                                                                    |
| Yiisoft\Router\RouteCollectorInterface                                               | Yiisoft\Router\RouteCollector                                                        |
| Yiisoft\Router\CurrentRoute                                                          | Yiisoft\Router\CurrentRoute                                                          |
| Psr\EventDispatcher\EventDispatcherInterface                                         | Yiisoft\EventDispatcher\Dispatcher\Dispatcher                                        |
| Psr\EventDispatcher\ListenerProviderInterface                                        | Yiisoft\EventDispatcher\Provider\Provider                                            |
| Yiisoft\Translator\TranslatorInterface                                               | Yiisoft\Translator\Translator                                                        |
| Yiisoft\Hydrator\AttributeHandling\ResolverFactory\AttributeResolverFactoryInterface | Yiisoft\Hydrator\AttributeHandling\ResolverFactory\ContainerAttributeResolverFactory |
| Yiisoft\Hydrator\ObjectFactory\ObjectFactoryInterface                                | Yiisoft\Hydrator\ObjectFactory\ContainerObjectFactory                                |
| Psr\Log\LoggerInterface                                                              | Yiisoft\Log\Logger                                                                   |
| Psr\Http\Message\RequestFactoryInterface                                             | HttpSoft\Message\RequestFactory                                                      |
| Psr\Http\Message\ServerRequestFactoryInterface                                       | HttpSoft\Message\ServerRequestFactory                                                |
| Psr\Http\Message\ResponseFactoryInterface                                            | HttpSoft\Message\ResponseFactory                                                     |
| Psr\Http\Message\StreamFactoryInterface                                              | HttpSoft\Message\StreamFactory                                                       |
| Psr\Http\Message\UriFactoryInterface                                                 | HttpSoft\Message\UriFactory                                                          |
| Psr\Http\Message\UploadedFileFactoryInterface                                        | HttpSoft\Message\UploadedFileFactory                                                 |
| Yiisoft\Rbac\ItemsStorageInterface                                                   | Yiisoft\Rbac\Php\ItemsStorage                                                        |
| Yiisoft\Rbac\AssignmentsStorageInterface                                             | Yiisoft\Rbac\Php\AssignmentsStorage                                                  |
| Yiisoft\Access\AccessCheckerInterface                                                | Yiisoft\Rbac\Manager                                                                 |
| Yiisoft\Router\RouteCollectionInterface                                              | Closure                                                                              |
| Http\Client\HttpClient                                                               | GuzzleHttp\Client                                                                    |
| Http\Client\HttpAsyncClient                                                          | Http\Adapter\Guzzle7\Client                                                          |
| translation.app                                                                      | translation.app                                                                      |
+--------------------------------------------------------------------------------------+--------------------------------------------------------------------------------------+
+----------------------- di-providers-console ------------------------+
| Group                      | Services                               |
+----------------------------+----------------------------------------+
| yiisoft/yii-debug/Debugger | Yiisoft\Yii\Debug\ProxyServiceProvider |
+----------------------------+----------------------------------------+
+- di-delegate... -+
| Group | Services |
+-------+----------+
| 0     | Closure  |
+-------+----------+
+----------------------------------------------- events-console ------------------------------------------------+
| Group                                                 | Services                                              |
+-------------------------------------------------------+-------------------------------------------------------+
| Yiisoft\Yii\Console\Event\ApplicationStartup          | Yiisoft\Yii\Console\Event\ApplicationStartup          |
| Yiisoft\Yii\Console\Event\ApplicationShutdown         | Yiisoft\Yii\Console\Event\ApplicationShutdown         |
| Symfony\Component\Console\Event\ConsoleCommandEvent   | Symfony\Component\Console\Event\ConsoleCommandEvent   |
| Symfony\Component\Console\Event\ConsoleErrorEvent     | Symfony\Component\Console\Event\ConsoleErrorEvent     |
| Symfony\Component\Console\Event\ConsoleTerminateEvent | Symfony\Component\Console\Event\ConsoleTerminateEvent |
| Yiisoft\Yii\Cycle\Event\AfterMigrate                  | Yiisoft\Yii\Cycle\Event\AfterMigrate                  |
+-------------------------------------------------------+-------------------------------------------------------+
+---- widgets -----+
| Group | Services |
+-------+----------+
+---- widgets-themes -----+
| Group      | Services   |
+------------+------------+
| bootstrap5 | bootstrap5 |
+------------+------------+
```

### Inspect a service

You can inspect a service by specifying its name as an argument:

```
❯ ./yii debug:container "Psr\Http\Message\UriFactoryInterface"

Psr\Http\Message\UriFactoryInterface
====================================

Psr\Http\Message\UriFactoryInterface
Yiisoft\Definitions\Reference#8425
(
    [Yiisoft\Definitions\Reference:id] => 'HttpSoft\\Message\\UriFactory'
    [Yiisoft\Definitions\Reference:optional] => false
)
```

> Note: make sure you use quotes if the service name contains a backslash.

### Inspect a config group

You can inspect a config group by specifying `--group $name` option:

```
❯ ./yii debug:container --group events-console
+----------------------------------------------- events-console ------------------------------------------------+
| Service                                               | Definition                                            |
+-------------------------------------------------------+-------------------------------------------------------+
| Symfony\Component\Console\Event\ConsoleCommandEvent   | Symfony\Component\Console\Event\ConsoleCommandEvent   |
| Symfony\Component\Console\Event\ConsoleErrorEvent     | Symfony\Component\Console\Event\ConsoleErrorEvent     |
| Symfony\Component\Console\Event\ConsoleTerminateEvent | Symfony\Component\Console\Event\ConsoleTerminateEvent |
| Yiisoft\Yii\Console\Event\ApplicationShutdown         | Yiisoft\Yii\Console\Event\ApplicationShutdown         |
| Yiisoft\Yii\Console\Event\ApplicationStartup          | Yiisoft\Yii\Console\Event\ApplicationStartup          |
| Yiisoft\Yii\Cycle\Event\AfterMigrate                  | Yiisoft\Yii\Cycle\Event\AfterMigrate                  |
+-------------------------------------------------------+-------------------------------------------------------+
```

### List all groups

You can list all groups by specifying `--groups` option:

```
❯ ./yii debug:container --groups
 ---------------------- 
  Groups                 
 ---------------------- 
  params-console        
  bootstrap-console     
  di-console            
  di-providers-console  
  di-delegates-console  
  events-console        
  widgets               
  widgets-themes        
 ---------------------- 
```
