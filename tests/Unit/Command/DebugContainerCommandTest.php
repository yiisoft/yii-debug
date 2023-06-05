<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Tester\CommandTester;
use Yiisoft\Config\Config;
use Yiisoft\Config\ConfigInterface;
use Yiisoft\Config\ConfigPaths;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Yii\Debug\Command\DebugContainerCommand;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\DebuggerIdGenerator;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class DebugContainerCommandTest extends TestCase
{
    public function testCommand()
    {
        $container = $this->createContainer();
        $idGenerator = new DebuggerIdGenerator();
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->never())->method('clear');
        $debugger = new Debugger($idGenerator, $storage, []);

        $command = new DebugContainerCommand($container, $debugger);

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);
    }

    private function createContainer(): ContainerInterface
    {
        $config = ContainerConfig::create()
            ->withDefinitions([
                LoggerInterface::class => NullLogger::class,
                ConfigInterface::class => [
                    'class' => Config::class,
                    '__construct()' => [
                        new ConfigPaths(dirname(__DIR__, 2).'/Support/Application/config'),
                    ],
                ]
            ]);
        return new Container($config);
    }
}
