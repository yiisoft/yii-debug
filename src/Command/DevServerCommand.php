<?php

/** @noinspection PhpComposerExtensionStubsInspection */

declare(strict_types=1);
declare(ticks=1);

namespace Yiisoft\Yii\Debug\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SignalRegistry\SignalRegistry;
use Symfony\Component\Console\Style\SymfonyStyle;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Debug\DevServer\Connection;

final class DevServerCommand extends Command
{
    public const COMMAND_NAME = 'dev';
    protected static $defaultName = self::COMMAND_NAME;

    protected static $defaultDescription = 'Runs PHP built-in web server';

    public function __construct(
        private SignalRegistry $signalRegistry,
        private string $address = '0.0.0.0',
        private int $port = 8890,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->setHelp(
                'In order to access server from remote machines use 0.0.0.0:8000. That is especially useful when running server in a virtual machine.'
            )
            ->addOption('address', 'a', InputOption::VALUE_OPTIONAL, 'Host to serve at', $this->address)
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Port to serve at', $this->port)
            ->addOption('env', 'e', InputOption::VALUE_OPTIONAL, 'It is only used for testing.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Yii3 Debug Server');
        $io->writeln('https://yiiframework.com' . "\n");

        $env = $input->getOption('env');
        if ($env === 'test') {
            return ExitCode::OK;
        }

        $socket = Connection::create();
        $socket->bind();

        //if (socket_recvfrom($socket, $buf, 64 * 1024, 0, $source) === false) {
        //    echo "recv_from failed";
        //}
        //var_dump($buf);
        //
        //return 0;
        //if (!@socket_bind($socket, $address, $port)) {
        //    $io->error(
        //        sprintf(
        //            'Address "%s" is already taken by another process.',
        //            $this->address . ':' . $this->port,
        //        )
        //    );
        //    $io->info(
        //        sprintf(
        //            'Would you like to kill the process and rerun?'
        //        )
        //    );
        //    $result = $io->ask('Would you like to kill the process and rerun? (Y/n)');
        //
        //    if ($result === 'Y') {
        //        // todo: change to finding a process by opened port
        //        $pid = shell_exec(
        //            sprintf(
        //                'ps | grep "php ./yii dev"',
        //            //$this->port,
        //            )
        //        );
        //        $io->info(
        //            sprintf(
        //                'Killing the process with ID: "%s".',
        //                $pid,
        //            )
        //        );
        //        //shell_exec('kill ' . $pid);
        //    }
        //    //return ExitCode::IOERR;
        //}

        $io->success(
            sprintf(
                'Listening on "%s:%d".',
                $this->address,
                $this->port,
            )
        );

        if (\function_exists('pcntl_signal')) {
            $io->success('Quit the server with CTRL-C or COMMAND-C.');

            \pcntl_signal(\SIGINT, static function () use ($socket): void {
                $socket->close();
                exit(1);
            });
        }

        foreach ($socket->read() as $message) {
            switch ($message[0]) {
                case Connection::TYPE_ERROR:
                    $io->writeln('Connection closed with error: ' . $message[1]);
                    break 2;
                default:
                    $io->writeln($message[1]);
                    $io->newLine();
            }
        }
        return ExitCode::OK;
    }
}
