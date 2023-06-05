<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Debug\Debugger;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

final class ResetCommand extends Command
{
    public const COMMAND_NAME = 'debug:reset';
    protected static $defaultName = self::COMMAND_NAME;

    public function __construct(
        private StorageInterface $storage,
        private Debugger $debugger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Clear debug data')
            ->setHelp('This command clears debug storage data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugger->stop();
        $this->storage->clear();

        return ExitCode::OK;
    }
}
