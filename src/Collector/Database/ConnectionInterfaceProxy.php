<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Database;

use Closure;
use Yiisoft\Cache\Dependency\Dependency;
use Yiisoft\Db\Command\CommandInterface;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Query\BatchQueryResultInterface;
use Yiisoft\Db\Query\QueryInterface;
use Yiisoft\Db\QueryBuilder\QueryBuilderInterface;
use Yiisoft\Db\Schema\QuoterInterface;
use Yiisoft\Db\Schema\SchemaInterface;
use Yiisoft\Db\Schema\TableSchemaInterface;
use Yiisoft\Db\Transaction\TransactionInterface;

final class ConnectionInterfaceProxy implements ConnectionInterface
{
    public function __construct(
        private ConnectionInterface $connection,
        private DatabaseCollector $collector
    ) {
    }

    public function beginTransaction(string $isolationLevel = null): TransactionInterface
    {
        return $this->connection->beginTransaction($isolationLevel);
    }

    public function cache(Closure $closure, int $duration = null, Dependency $dependency = null): mixed
    {
        return $this->connection->cache($closure, $duration, $dependency);
    }

    public function createBatchQueryResult(QueryInterface $query, bool $each = false): BatchQueryResultInterface
    {
        return $this->connection->createBatchQueryResult($query, $each);
    }

    public function createCommand(string $sql = null, array $params = []): CommandInterface
    {
        [$callStack] = debug_backtrace();

        $this->collector->collect($sql, $params, $callStack['file'] . ':' . $callStack['line']);

        return $this->connection->createCommand($sql, $params);
    }

    public function createTransaction(): TransactionInterface
    {
        return $this->connection->createTransaction();
    }

    public function close(): void
    {
        $this->connection->close();
    }

    public function getCacheKey(): array
    {
        return $this->connection->getCacheKey();
    }

    public function getName(): string
    {
        return $this->connection->getName();
    }

    public function getLastInsertID(string $sequenceName = null): string
    {
        return $this->connection->getLastInsertID($sequenceName);
    }

    public function getQueryBuilder(): QueryBuilderInterface
    {
        return $this->connection->getQueryBuilder();
    }

    public function getQuoter(): QuoterInterface
    {
        return $this->connection->getQuoter();
    }

    public function getSchema(): SchemaInterface
    {
        return $this->connection->getSchema();
    }

    public function getServerVersion(): string
    {
        return $this->connection->getServerVersion();
    }

    public function getTablePrefix(): string
    {
        return $this->connection->getTablePrefix();
    }

    public function getTableSchema(string $name, bool $refresh = false): TableSchemaInterface|null
    {
        return $this->connection->getTableSchema($name, $refresh);
    }

    public function getTransaction(): TransactionInterface|null
    {
        return $this->connection->getTransaction();
    }

    public function isActive(): bool
    {
        return $this->connection->isActive();
    }

    public function isSavepointEnabled(): bool
    {
        return $this->connection->isSavepointEnabled();
    }

    public function noCache(Closure $closure): mixed
    {
        return $this->connection->noCache($closure);
    }

    public function notProfiler(): void
    {
        $this->connection->notProfiler();
    }

    public function open(): void
    {
        $this->connection->open();
    }

    public function queryCacheEnable(bool $value): void
    {
        $this->connection->queryCacheEnable($value);
    }

    public function quoteValue(mixed $value): mixed
    {
        return $this->connection->quoteValue($value);
    }

    public function setEnableSavepoint(bool $value): void
    {
        $this->connection->setEnableSavepoint($value);
    }

    public function setTablePrefix(string $value): void
    {
        $this->connection->setTablePrefix($value);
    }

    public function transaction(Closure $closure, string $isolationLevel = null): mixed
    {
        return $this->connection->transaction($closure, $isolationLevel);
    }
}
