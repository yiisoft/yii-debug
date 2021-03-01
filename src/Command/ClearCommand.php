<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

class ClearCommand extends Command
{
    private StorageInterface $storage;

    protected static $defaultName = 'debug/reset';

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
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
        $this->storage->clear();

        return ExitCode::OK;
    }
}
