<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\data\repositories;

use DI\Attribute\Inject;
use Humbrain\Framework\data\ORM\QueryBuilder;
use Humbrain\Framework\data\entities\AbstractEntities;
use PDO;
use PDOStatement;

/**
 * Class AbstractRepository
 * @package Humbrain\Framework\data\repositories
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
abstract class AbstractRepository
{
    protected PDO $db;
    protected AbstractEntities $entity;
    protected QueryBuilder $queryBuilder;

    public function __construct(AbstractEntities $entity, #[Inject] PDO $db)
    {
        $this->db = $db;
        $this->entity = $entity;
        $this->queryBuilder = new QueryBuilder($entity::$TABLE_NAME);
    }

    /**
     * Find by id
     * @param int $id
     * @return AbstractEntities
     */
    final public function find(int $id): AbstractEntities
    {
        $queryBuilder = $this
            ->queryBuilder
            ->select(null, "*")
            ->where('id', '=', $id);
        $query = $this->db->prepare($queryBuilder->getQuery());
        $this->bindParams($query, $queryBuilder->getValues());
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS, get_class($this->entity));
        return $query->fetch();
    }

    /**
     * Bind params
     * @param PDOStatement $query
     * @param array $params
     */
    final protected function bindParams(PDOStatement $query, array $params): void
    {
        foreach ($params as $key => $value) {
            if (is_object($value) || is_array($value)) :
                $value = json_encode($value);
            endif;
            $query->bindParam(($key + 1), $value, $this->getPDOType($value));
        }
    }

    /**
     * Get PDO type
     * @param mixed $value
     * @return int
     */
    final protected function getPDOType(mixed $value): int
    {
        return match (gettype($value)) {
            'boolean' => PDO::PARAM_BOOL,
            'integer' => PDO::PARAM_INT,
            'NULL' => PDO::PARAM_NULL,
            default => PDO::PARAM_STR,
        };
    }

    /**
     * Find all
     * @return AbstractEntities[]
     */
    final public function findAll(): array
    {
        $queryBuilder = $this
            ->queryBuilder
            ->select(null, "*");
        $query = $this->db->prepare($queryBuilder->getQuery());
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS, get_class($this->entity));
        return $query->fetchAll();
    }

    /**
     * Insert or update entity
     * @return AbstractEntities
     */
    final public function save(): AbstractEntities
    {
        if (empty($this->entity->id)) :
            $queryBuilder = $this
                ->queryBuilder
                ->insert();
        else :
            $queryBuilder = $this
                ->queryBuilder
                ->update()
                ->where('id', '=', $this->entity->id);
        endif;
        foreach ($this->entity as $key => $value) {
            if ($key !== 'id') {
                $queryBuilder->addData($key, $value);
            }
        }
        $query = $this->db->prepare($queryBuilder->getQuery());
        $this->bindParams($query, $queryBuilder->getValues());
        $query->execute();
        if (empty($this->entity->id)) :
            $this->entity->id = $this->db->lastInsertId();
        endif;
        return $this->entity;
    }

    /**
     * Delete entity
     * @return bool
     */
    final public function delete(): bool
    {
        $queryBuilder = $this
            ->queryBuilder
            ->delete()
            ->where('id', '=', $this->entity->id);
        $query = $this->db->prepare($queryBuilder->getQuery());
        $this->bindParams($query, $queryBuilder->getValues());
        return $query->execute();
    }

    /**
     * Custom query
     * @param string $query
     * @param array $params
     * @return AbstractEntities[]
     */
    final public function customQuery(string $query, array $params = []): array
    {
        $query = $this->db->prepare($query);
        $this->bindParams($query, $params);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_CLASS, get_class($this->entity));
        return $query->fetchAll();
    }
}
