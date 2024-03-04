<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Command;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\VarDumper as SymfonyVarDumper;
use Yiisoft\Config\ConfigInterface;
use Yiisoft\VarDumper\VarDumper;
use Yiisoft\Yii\Console\ExitCode;
use Yiisoft\Yii\Debug\Debugger;

final class DebugEventsCommand extends Command
{
    public const COMMAND_NAME = 'debug:events';
    protected static $defaultName = self::COMMAND_NAME;

    public function __construct(
        private ContainerInterface $container,
        private Debugger $debugger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show information about events and listeners')
            ->addArgument('id', InputArgument::IS_ARRAY, 'Service ID')
            ->addOption('groups', null, InputOption::VALUE_NONE, 'Show groups')
            ->addOption('group', 'g', InputOption::VALUE_REQUIRED, 'Show group');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->debugger->stop();
        $config = $this->container->get(ConfigInterface::class);

        $io = new SymfonyStyle($input, $output);

        if ($input->hasOption('groups') && $input->getOption('groups')) {
            $build = $this->getConfigBuild($config);
            $groups = array_keys($build);
            sort($groups);

            $io->table(['Groups'], array_map(fn ($group) => [$group], $groups));

            return ExitCode::OK;
        }
        if ($input->hasOption('group') && !empty($group = $input->getOption('group'))) {
            $data = $config->get($group);
            ksort($data);
            $table = new Table($output);

            foreach ($data as $event => $listeners) {
                $io->title($event);
                foreach ($listeners as $listener) {
                    if (is_callable($listener) && !is_array($listener)) {
                        SymfonyVarDumper::dump($this->export($listener));
                    } else {
                        SymfonyVarDumper::dump($listener);
                    }
                }
                $table->render();
                $io->newLine();
            }
            return ExitCode::OK;
        }

        $data = [];
        if ($config->has('events')) {
            $data = array_merge($data, $config->get('events'));
        }
        if ($config->has('events-console')) {
            $data = array_merge($data, $config->get('events-console'));
        }
        $rows = [];
        foreach ($data as $event => $listeners) {
            $rows[] = [
                $event,
                is_countable($listeners) ? count($listeners) : 0,
                implode(
                    "\n",
                    array_map(function (mixed $listener) {
                        if (is_array($listener)) {
                            return sprintf(
                                '%s::%s',
                                $listener[0],
                                $listener[1]
                            );
                        }
                        return $this->export($listener);
                    }, $listeners)
                ),
            ];
        }
        $table = new Table($output);
        $table
            ->setHeaders(['Event', 'Count', 'Listeners'])
            ->setRows($rows);
        $table->render();

        return ExitCode::OK;
    }

    private function getConfigBuild(mixed $config): array
    {
        $reflection = new ReflectionClass($config);
        $buildReflection = $reflection->getProperty('build');
        $buildReflection->setAccessible(true);
        return $buildReflection->getValue($config);
    }

    protected function export(mixed $value): string
    {
        return VarDumper::create($value)->asString();
    }
}
