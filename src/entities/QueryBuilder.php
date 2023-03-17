<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\entities;

use PDO;
use PDOStatement;

/**
 * Class QueryBuilder
 * @package Humbrain\Framework\entities
 */
class QueryBuilder implements IQueryBuilder
{
    private string $type;
    private string $table;
    private array $columns = [];
    private array $values = [];
    private array $sets = [];
    private array $conditions = [];
    private array $clauses = [
        'select' => [],
        'insert' => [],
        'update' => [],
        'delete' => [],
        'from' => [],
        'set' => [],
        'where' => [],
        'join' => [],
        'groupBy' => [],
        'having' => [],
        'orderBy' => [],
        'limit' => [],
    ];

    /**
     * @inheritDoc
     *
     **/
    final public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function addColumn(string $column): self
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function addValue($value): self
    {
        $this->values[] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function addSet(string $column, $value): self
    {
        $this->sets[$column] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function addCondition(string $column, string $operator, $value): self
    {
        $this->conditions[] = ['column' => $column, 'operator' => $operator, 'value' => $value,];
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function select(string ...$columns): self
    {
        $this->type = 'select';
        $this->columns = $columns;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function insert(): self
    {
        $this->type = 'insert';
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function update(): self
    {
        $this->type = 'update';
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function delete(): self
    {
        $this->type = 'delete';
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function from(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function set(array $sets): self
    {
        $this->sets = $sets;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function where(array $conditions): self
    {
        $this->conditions = $conditions;
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function join(string $table, string $on, string $type = 'INNER'): self
    {
        $joinClause = sprintf('%s JOIN %s ON %s', $type, $table, $on);
        $this->addClause('join', $joinClause);
        return $this;
    }

    private function addClause(string $key, ...$values): void
    {
        $this->clauses[$key] = array_merge($this->clauses[$key], $values);
    }

    /**
     * @inheritDoc
     *
     **/
    final public function groupBy(string ...$columns): self
    {
        $this->addClause('groupBy', ...$columns);
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function having(string $condition): self
    {
        $this->addClause('having', $condition);
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $orderByClause = sprintf('%s %s', $column, $direction);
        $this->addClause('orderBy', $orderByClause);
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function limit(int $limit, int $offset = 0): self
    {
        $this->addClause('limit', $limit, $offset);
        return $this;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function getSQL(): string
    {
        $sql = '';
        switch ($this->type) {
            case 'select':
                $sql = $this->buildSelect();
                break;
            case 'insert':
                $sql = $this->buildInsert();
                break;
            case 'update':
                $sql = $this->buildUpdate();
                break;
            case 'delete':
                $sql = $this->buildDelete();
                break;
        }
        return $sql;
    }

    private function buildSelect(): string
    {
        $sql = sprintf('SELECT %s FROM %s', implode(', ', $this->columns), $this->table);
        $sql .= $this->buildJoin();
        $sql .= $this->buildWhere();
        $sql .= $this->buildGroupBy();
        $sql .= $this->buildHaving();
        $sql .= $this->buildOrderBy();
        $sql .= $this->buildLimit();
        return $sql;
    }

    private function buildJoin(): string
    {
        $joinClauses = implode(' ', $this->clauses['join']);
        return empty($joinClauses) ? '' : ' ' . $joinClauses;
    }

    private function buildWhere(): string
    {
        $whereClause = '';
        if (!empty($this->conditions)) {
            $whereConditions = [];
            foreach ($this->conditions as $condition) {
                $whereConditions[] = sprintf('%s %s ?', $condition['column'], $condition['operator']);
                $this->addClause('where', $condition['value']);
            }
            $whereClause = ' WHERE ' . implode(' AND ', $whereConditions);
        }
        return $whereClause;
    }

    private function buildGroupBy(): string
    {
        $groupByClause = '';
        if (!empty($this->clauses['groupBy'])) {
            $groupByClause = ' GROUP BY ' . implode(', ', $this->clauses['groupBy']);
        }
        return $groupByClause;
    }

    private function buildHaving(): string
    {
        $havingClause = '';
        if (!empty($this->clauses['having'])) {
            $havingClause = ' HAVING ' . implode(' AND ', $this->clauses['having']);
        }
        return $havingClause;
    }

    private function buildOrderBy(): string
    {
        $orderByClause = '';
        if (!empty($this->clauses['orderBy'])) {
            $orderByClause = ' ORDER BY ' . implode(', ', $this->clauses['orderBy']);
        }
        return $orderByClause;
    }

    private function buildLimit(): string
    {
        $limitClause = '';
        if (!empty($this->clauses['limit'])) {
            $limit = $this->clauses['limit'][0];
            $offset = $this->clauses['limit'][1] ?? 0;
            $limitClause = sprintf(' LIMIT %d OFFSET %d', $limit, $offset);
        }
        return $limitClause;
    }

    private function buildInsert(): string
    {
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, implode(', ', array_keys($this->sets)), implode(', ', array_fill(0, count($this->sets), '?')));
        $this->addClause('insert', ...array_values($this->sets));
        return $sql;
    }

    private function buildUpdate(): string
    {
        $sets = array_map(fn($column) => sprintf('%s = ?', $column), array_keys($this->sets));
        $sql = sprintf('UPDATE %s SET %s', $this->table, implode(', ', $sets));
        $this->addClause('update', ...array_values($this->sets));
        $sql .= $this->buildWhere();
        $sql .= $this->buildLimit();
        return $sql;
    }

    private function buildDelete(): string
    {
        $sql = sprintf('DELETE FROM %s', $this->table);
        $sql .= $this->buildWhere();
        $sql .= $this->buildLimit();
        return $sql;
    }

    /**
     * @inheritDoc
     *
     **/
    final public function bindParams(PDOStatement $statement): void
    {
        $index = 1;
        foreach ($this->clauses as $key => $values) {
            if (!empty($values)) {
                foreach ((array)$values as $value) {
                    $type = $this->getPDOParamType($value);
                    $statement->bindValue($index, $value, $type);
                    $index++;
                }
            }
        }
    }

    private function getPDOParamType(mixed $value): int
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        }
        if (is_bool($value)) {
            return PDO::PARAM_BOOL;
        }
        if (is_null($value)) {
            return PDO::PARAM_NULL;
        }
        if (is_float($value)) {
            return PDO::PARAM_INT;
        }
        if (is_string($value)) {
            return PDO::PARAM_STR;
        }
        if (is_object($value)) {
            return PDO::PARAM_STR;
        }
        if (is_array($value)) {
            return PDO::PARAM_STR;
        }
        return PDO::PARAM_STR;
    }
}
