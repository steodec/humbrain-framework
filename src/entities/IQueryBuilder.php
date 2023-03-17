<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\entities;

use PDOStatement;

/**
 * Interface IQueryBuilder
 * @package Humbrain\Framework\entities
 */
interface IQueryBuilder
{
    /**
     * Sets the type of the query.
     *
     * @param string $type The type of the query.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function setType(string $type): self;

    /**
     * Sets the table used in the query.
     *
     * @param string $table The name of the table.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function setTable(string $table): self;

    /**
     * Adds a column to the query.
     *
     * @param string $column The name of the column.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function addColumn(string $column): self;

    /**
     * Adds a value to the query.
     *
     * @param mixed $value The value to add.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function addValue($value): self;

    /**
     * Adds a set to the query.
     *
     * @param string $column The name of the column.
     * @param mixed $value The value to set.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function addSet(string $column, $value): self;

    /**
     * Adds a condition to the query.
     *
     * @param string $column The name of the column.
     * @param string $operator The comparison operator.
     * @param mixed $value The value to compare to.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function addCondition(string $column, string $operator, $value): self;

    /**
     * Sets the type of the query to SELECT and adds the specified columns.
     *
     * @param string ...$columns The names of the columns to select.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function select(string ...$columns): self;

    /**
     * Sets the type of the query to INSERT.
     *
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function insert(): self;

    /**
     * Sets the type of the query to UPDATE.
     *
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function update(): self;

    /**
     * Sets the type of the query to DELETE.
     *
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function delete(): self;

    /**
     * Sets the table used in the query.
     *
     * @param string $table The name of the table.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function from(string $table): self;

    /**
     * Adds multiple sets to the query.
     *
     * @param array $sets An associative array of column => value pairs to set.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function set(array $sets): self;

    /**
     * Adds multiple conditions to the query.
     *
     * @param array $conditions An associative array of column => value pairs to compare to.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function where(array $conditions): self;

    /**
     * Adds a JOIN clause to the query.
     *
     * @param string $table The name of the table to join.
     * @param string $on The ON clause for the join.
     * @param string $type The type of the join (e.g. INNER, LEFT, RIGHT).
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function join(string $table, string $on, string $type = 'INNER'): self;

    /**
     * Adds a GROUP BY clause to the query.
     *
     * @param string ...$columns The names of the columns to group by.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function groupBy(string ...$columns): self;

    /**
     * Adds a HAVING clause to the query.
     *
     * @param string $condition The condition for the HAVING clause.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function having(string $condition): self;

    /**
     * Adds an ORDER BY clause to the query.
     *
     * @param string $column The name of the column to order by.
     * @param string $direction The direction to order by (ASC or DESC).
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function orderBy(string $column, string $direction = 'ASC'): self;

    /**
     * Sets the limit of the query.
     *
     * @param int $limit The maximum number of rows to return.
     * @param int $offset The number of rows to skip before returning.
     * @return QueryBuilder The current QueryBuilder object.
     */
    public function limit(int $limit, int $offset = 0): self;

    /**
     * Builds the SQL query.
     *
     * @return string The built SQL query.
     */
    public function getSQL(): string;

    /**
     * Binds the parameters to a prepared statement.
     *
     * @param PDOStatement $statement The prepared statement to bind the parameters to.
     */
    public function bindParams(PDOStatement $statement): void;


}