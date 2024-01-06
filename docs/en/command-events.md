# `debug:events`

The `debug/events` command displays all registered events and their listeners.

For example:

```
❯ ./yii debug:events
+-------------------------------------------------------+-------+----------------------------------------------------------------------+
| Event                                                 | Count | Listeners                                                            |
+-------------------------------------------------------+-------+----------------------------------------------------------------------+
| Yiisoft\Yii\Console\Event\ApplicationStartup          | 3     | static fn (\App\Timer $timer) => $timer->start('overall')            |
|                                                       |       | Yiisoft\Yii\Debug\Debugger::startup                                  |
|                                                       |       | Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector::collect |
| Yiisoft\Yii\Console\Event\ApplicationShutdown         | 2     | Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector::collect |
|                                                       |       | Yiisoft\Yii\Debug\Debugger::shutdown                                 |
| Symfony\Component\Console\Event\ConsoleCommandEvent   | 2     | Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector::collect |
|                                                       |       | Yiisoft\Yii\Debug\Collector\Console\CommandCollector::collect        |
| Symfony\Component\Console\Event\ConsoleErrorEvent     | 3     | Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector::collect |
|                                                       |       | Yiisoft\Yii\Debug\Collector\Console\CommandCollector::collect        |
|                                                       |       | Yiisoft\Yii\Console\ErrorListener::onError                           |
| Symfony\Component\Console\Event\ConsoleTerminateEvent | 3     | Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector::collect |
|                                                       |       | Yiisoft\Yii\Debug\Collector\Console\CommandCollector::collect        |
|                                                       |       | static function (\Psr\Log\LoggerInterface $logger): void {           |
|                                                       |       |     if ($logger instanceof \Yiisoft\Log\Logger) {                    |
|                                                       |       |         $logger->flush(true);                                        |
|                                                       |       |     }                                                                |
|                                                       |       | }                                                                    |
| Yiisoft\Yii\Cycle\Event\AfterMigrate                  | 1     | Yiisoft\Yii\Cycle\Listener\MigrationListener::onAfterMigrate         |
+-------------------------------------------------------+-------+----------------------------------------------------------------------+
```
### List all groups

You can list all groups by specifying `--groups` option:

```
❯ ./yii debug:events --groups
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

> Note: unfortunately, the command cannot recognize only event groups automatically, so you will have all groups listed.

### Inspect a group

You can inspect a group by specifying its name as an argument:

```
❯ ./yii debug:events --group events-console

Symfony\Component\Console\Event\ConsoleCommandEvent
===================================================

array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector"
  1 => "collect"
]
array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\CommandCollector"
  1 => "collect"
]

Symfony\Component\Console\Event\ConsoleErrorEvent
=================================================

array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector"
  1 => "collect"
]
array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\CommandCollector"
  1 => "collect"
]
array:2 [
  0 => "Yiisoft\Yii\Console\ErrorListener"
  1 => "onError"
]

Symfony\Component\Console\Event\ConsoleTerminateEvent
=====================================================

array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector"
  1 => "collect"
]
array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\CommandCollector"
  1 => "collect"
]
"""
static function (\Psr\Log\LoggerInterface $logger): void {\n
    if ($logger instanceof \Yiisoft\Log\Logger) {\n
        $logger->flush(true);\n
    }\n
}
"""

Yiisoft\Yii\Console\Event\ApplicationShutdown
=============================================

array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector"
  1 => "collect"
]
array:2 [
  0 => "Yiisoft\Yii\Debug\Debugger"
  1 => "shutdown"
]

Yiisoft\Yii\Console\Event\ApplicationStartup
============================================

"static fn (\App\Timer $timer) => $timer->start('overall')"
array:2 [
  0 => "Yiisoft\Yii\Debug\Debugger"
  1 => "startup"
]
array:2 [
  0 => "Yiisoft\Yii\Debug\Collector\Console\ConsoleAppInfoCollector"
  1 => "collect"
]

Yiisoft\Yii\Cycle\Event\AfterMigrate
====================================

array:2 [
  0 => "Yiisoft\Yii\Cycle\Listener\MigrationListener"
  1 => "onAfterMigrate"
]
```
