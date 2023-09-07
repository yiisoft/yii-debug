<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\DevServer;

use Yiisoft\VarDumper\HandlerInterface;

final class VarDumperHandler implements HandlerInterface
{
    public Connection $connection;

    public function __construct()
    {
        $this->connection = Connection::create();
    }

    public function handle(mixed $variable, int $depth, bool $highlight = false): void
    {
        $this->connection->broadcast(json_encode($variable, JSON_THROW_ON_ERROR));
    }
}
