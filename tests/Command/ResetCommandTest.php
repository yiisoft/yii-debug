<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Yiisoft\Yii\Debug\Command\ResetCommand;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

class ResetCommandTest extends TestCase
{
    public function testCommand()
    {
        $storage = $this->createMock(StorageInterface::class);
        $storage->expects($this->once())->method('clear');
        
        $command = new ResetCommand($storage);

        $commandTester = new CommandTester($command);

        $commandTester->execute([]);
    }
}
