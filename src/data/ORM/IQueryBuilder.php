<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\data\ORM;

/**
 * Interface IQueryBuilder
 * @author Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 * @package Humbrain\Framework\data\ORM
 */
interface IQueryBuilder
{
    /**
     * Select query
     * @param string|null $table
     * @param array $fields
     * @return IQueryBuilder
     */
    public function select(string|null $table, string ...$fields): IQueryBuilder;

    /**
     * Insert query
     * @param string $table
     * @param array $fields
     * @return IQueryBuilder
     */
    public function insert(string $table, array $fields): IQueryBuilder;

    /**
     * Update query
     * @param string $table
     * @param array $fields
     * @return IQueryBuilder
     */
    public function update(string $table, array $fields): IQueryBuilder;

    /**
     * Delete query
     * @param string $table
     * @return IQueryBuilder
     */
    public function delete(string $table): IQueryBuilder;

    /**
     * Where query
     * @param string $field
     * @param string $operator
     * @param string $value
     * @param string $andOrOr
     * @return IQueryBuilder
     */
    public function where(string $field, string $operator, string $value, string $andOrOr = "AND"): IQueryBuilder;

    /**
     * OrderBy query
     * @param string $field
     * @param string $direction
     * @return IQueryBuilder
     */
    public function orderBy(string $field, string $direction): IQueryBuilder;

    /**
     * Limit query
     * @param int $limit
     * @return IQueryBuilder
     */
    public function limit(int $limit): IQueryBuilder;

    /**
     * Offset query
     * @param int $offset
     * @return IQueryBuilder
     */
    public function offset(int $offset): IQueryBuilder;

    /**
     * Get query
     * @return string
     */
    public function getQuery(): string;

    /**
     * Get params
     * @return array
     */
    public function getParams(): array;
}
