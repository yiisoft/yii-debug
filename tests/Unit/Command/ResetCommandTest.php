<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Unit\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Yiisoft\Yii\Debug\Command\DebugResetCommand;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class ResetCommandTest extends TestCase
{
    public function testCommand(): void
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())->method('clear');
        $debugger = new Debugger($storage, []);

        $command = new DebugResetCommand($storage, $debugger);

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);
    }
}
