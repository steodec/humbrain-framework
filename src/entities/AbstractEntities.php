<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\entities;

use Exception;
use PDO;
use PDOStatement;
use stdClass;

/**
 * Class AbstractEntities
 * @package Humbrain\Framework\entities
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
abstract class AbstractEntities
{
    public const TABLE_NAME = null;
    public int $id;
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Saves the current instance of an AbstractEntities class to the database.
     * If the instance has an ID property set, it updates the existing record in the database.
     * If the instance does not have an ID property set, it inserts a new record into the database.
     * @return AbstractEntities Returns the current instance of the AbstractEntities class after the save operation.
     */
    final public function saveEntity(): AbstractEntities
    {
        if ($this->id) :
            $query = $this->createQueryBuilder()->setTable($this::TABLE_NAME)->update()->addSet('id', $this->id);
            foreach (get_object_vars($this) as $propName => $propValue) :
                if ($propName !== 'id' && isset($propValue)) :
                    $query->addSet($propName, $propValue);
                endif;
            endforeach;
        else :
            $query = $this->createQueryBuilder()->setTable($this::TABLE_NAME)->insert();
            foreach (get_object_vars($this) as $propName => $propValue) :
                if ($propName !== 'id' && isset($propValue)) :
                    $query->addColumn($propName)->addValue($propValue);
                endif;
            endforeach;
        endif;

        $this->prepareAndExecuteQuery($query);

        if (!$this->id) :
            $this->id = $this->pdo->lastInsertId();
        endif;

        return $this;
    }

    /**
     * Return the QueryBuilder instance.
     * @return QueryBuilder
     */
    final public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder();
    }

    /**
     * Prepares and executes the given query using PDO.
     * @param QueryBuilder $query The query to prepare and execute.
     * @return PDOStatement The PDOStatement instance representing the prepared and executed query.
     */
    protected function prepareAndExecuteQuery(QueryBuilder $query): PDOStatement
    {
        $sql = $query->getSQL();
        $stmt = $this->pdo->prepare($sql);
        $query->bindParams($stmt);
        $stmt->execute();
        return $stmt;
    }


    /**
     * This function takes an instance of AbstractEntities class and an integer ID as arguments.
     * It then constructs a query to fetch data from the database table associated with the given
     * AbstractEntities instance where the ID matches the given ID argument. The query is executed,
     *  and the result is returned as an instance of the AbstractEntities class.
     *
     * @param int $id
     *
     * @return AbstractEntities
     */
    final public function fetchById(int $id): AbstractEntities
    {
        $query = $this->
        createQueryBuilder()
            ->setTable($this::TABLE_NAME)
            ->select()
            ->addColumn('*')
            ->addCondition('id', '=', $id);

        $stmt = $this->prepareAndExecuteQuery($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        return $stmt->fetch();
    }

    /**
     * Execute a custom SQL query and fetch all rows as an iterable of objects.
     * @param string $sql The SQL query to execute.
     * @param array $params An optional array of parameters to bind to the SQL query.
     * @return iterable An iterable of objects containing the data fetched from the database table.
     */
    final public function customQueryFetchAll(string $sql, array $params = []): iterable
    {
        $stmt = $this->customQuery($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

    final public function customQuery(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * @param AbstractEntities $entities
     * @return AbstractEntities[]
     */
    final public function fetchAll(): iterable
    {
        $query = $this->createQueryBuilder()->setTable($this::TABLE_NAME)->select()->addColumn('*');

        $stmt = $this->prepareAndExecuteQuery($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        return $stmt->fetchAll();
    }

    /**
     * Deletes the entity from the database table.
     * This function deletes the row in the database table associated with the instance of the calling class.
     * It constructs a query using the query builder to delete the row where the ID matches the ID of the instance.
     * @return void
     * @throws Exception if there is an error executing the query.
     */
    final public function deleteEntity(): void
    {
        $query = $this
            ->createQueryBuilder()
            ->setTable($this::TABLE_NAME)
            ->delete()
            ->addCondition('id', '=', $this->id);

        $this->prepareAndExecuteQuery($query);
    }
}
