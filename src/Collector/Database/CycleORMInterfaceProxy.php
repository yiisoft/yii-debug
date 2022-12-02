<?php

declare(strict_types=1);

namespace Yiisoft\Yii\Debug\Collector\Database;

use Cycle\ORM\FactoryInterface;
use Cycle\ORM\Heap\HeapInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\MapperInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RelationMap;
use Cycle\ORM\RepositoryInterface;
use Cycle\ORM\Select\SourceInterface;
use Cycle\ORM\Transaction\CommandGeneratorInterface;

final class CycleORMInterfaceProxy implements ORMInterface
{
    public function __construct(
        private ORMInterface $orm,
        private CycleCollector $collector
    ) {
    }

    public function get(string $role, array $scope, bool $load = true): ?object
    {
        $this->collector->collect('get', $role, $scope, $load);
        return $this->orm->get($role, $scope, $load);
    }

    public function getIndexes(string $entity): array
    {
        $this->collector->collect('getIndexes', $entity);
        return $this->orm->getIndexes($entity);
    }

    public function resolveRole(object|string $entity): string
    {
        $this->collector->collect('resolveRole', $entity);
        return $this->orm->resolveRole($entity);
    }

    public function make(string $role, array $data = [], int $status = Node::NEW, bool $typecast = false): object
    {
        $this->collector->collect('make', $role, $data, $status, $typecast);
        return $this->orm->make($role, $data, $status, $typecast);
    }

    public function getFactory(): FactoryInterface
    {
        $this->collector->collect('getFactory');
        return $this->orm->getFactory();
    }

    public function getCommandGenerator(): CommandGeneratorInterface
    {
        $this->collector->collect('getCommandGenerator');
        return $this->orm->getCommandGenerator();
    }

    public function getService(string $class): object
    {
        $this->collector->collect('getService', $class);
        return $this->orm->getService($class);
    }

    public function getSchema(): \Cycle\ORM\SchemaInterface
    {
        $this->collector->collect('getSchema');
        return $this->orm->getSchema();
    }

    public function getHeap(): HeapInterface
    {
        $this->collector->collect('getHeap');
        return $this->orm->getHeap();
    }

    public function with(
        ?\Cycle\ORM\SchemaInterface $schema = null,
        ?FactoryInterface $factory = null,
        ?HeapInterface $heap = null
    ): ORMInterface {

        $this->collector->collect('with', $schema, $factory, $heap);
        return new self($this->orm->with($schema, $factory, $heap), $this->collector);
    }

    public function getMapper(object|string $entity): MapperInterface
    {
        $this->collector->collect('getMapper', $entity);
        return $this->orm->getMapper($entity);
    }

    public function getRepository(object|string $entity): RepositoryInterface
    {
//        var_dump($this->orm);
//        exit();
//        throw new \InvalidArgumentException();
        $this->collector->collect('getRepository', $entity);
        return $this->orm->getRepository($entity);
    }

    public function getRelationMap(string $entity): RelationMap
    {
        $this->collector->collect('getRelationMap', $entity);
        return $this->orm->getRelationMap($entity);
    }

    public function getSource(string $entity): SourceInterface
    {
        $this->collector->collect('getSource', $entity);
        return $this->orm->getSource($entity);
    }
}
