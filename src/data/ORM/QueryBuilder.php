<?php

/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\data\ORM;

use PHPUnit\Logging\Exception;

class QueryBuilder
{
    private string $query;
    private array $where = [];
    private array $orderBy = [];
    private array $limit = [];
    private array $offset = [];
    private string $table;
    private array $data = [];
    private string $type;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    final public function select(null|string $table = null, string ...$fields): QueryBuilder
    {
        $this->query = 'SELECT ';
        $this->type = 'select';
        if (empty($fields)) {
            $this->query .= '*';
        } else {
            $this->query .= implode(', ', $fields);
        }
        $this->table = $table ?? $this->table;
        return $this;
    }

    final public function insert(string|null $table = null): QueryBuilder
    {
        $this->query = 'INSERT INTO ';
        $this->type = 'insert';
        $this->table = $table ?? $this->table;
        return $this;
    }

    final public function update(string|null $table = null): QueryBuilder
    {
        $this->query = 'UPDATE ';
        $this->type = 'update';
        $this->table = $table ?? $this->table;
        return $this;
    }

    final public function delete(string|null $table = null): QueryBuilder
    {
        $this->query = 'DELETE FROM ';
        $this->type = 'delete';
        $this->table = $table ?? $this->table;
        return $this;
    }

    final public function where(string $field, string $operator, mixed $value, string $andOrOr = "AND"): QueryBuilder
    {
        $this->where[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
            'andOrOr' => $andOrOr
        ];
        $this->addData($field, $value);
        return $this;
    }

    final public function addData(string $column, mixed $value): QueryBuilder
    {
        $this->data[] = [
            'column' => $column,
            'value' => $value
        ];
        return $this;
    }

    final public function orderBy(string $field, string $direction): QueryBuilder
    {
        $this->orderBy[] = [
            'field' => $field,
            'direction' => $direction
        ];
        return $this;
    }

    final public function limit(int $limit): QueryBuilder
    {
        $this->limit[] = $limit;
        return $this;
    }

    final public function offset(int $offset): QueryBuilder
    {
        $this->offset[] = $offset;
        return $this;
    }

    final public function getQuery(): string
    {
        switch ($this->type) {
            case 'select':
                return $this->selectQuery();
            case 'update':
            case 'insert':
                return $this->saveQuery();
            case 'delete':
                return $this->deleteQuery();
        }
        throw new Exception("Invalid query type");
    }

    private function selectQuery(): string
    {
        $this->query .= " FROM {$this->table}";
        $this->whereBuilder();
        if (!empty($this->orderBy)) {
            $this->query .= " ORDER BY ";
            foreach ($this->orderBy as $orderBy) {
                $this->query .= "{$orderBy['field']} {$orderBy['direction']}, ";
            }
            $this->query = rtrim($this->query, ', ');
        }
        if (!empty($this->limit)) {
            $this->query .= " LIMIT {$this->limit[0]}";
        }
        if (!empty($this->offset)) {
            $this->query .= " OFFSET {$this->offset[0]}";
        }
        return $this->query;
    }

    /**
     * @return void
     */
    private function whereBuilder(): void
    {
        if (!empty($this->where)) {
            $this->query .= " WHERE ";
            foreach ($this->where as $where) {
                $this->query .= "{$where['field']} {$where['operator']} ? {$where['andOrOr']} ";
            }
            $this->query = rtrim($this->query, 'AND ');
            $this->query = rtrim($this->query, 'OR ');
        }
    }

    private function saveQuery(): string
    {
        $this->query .= "{$this->table} SET ";
        $this->query .= implode(', ', array_map(fn($data) => "{$data['column']} = ?", $this->data));
        $this->whereBuilder();
        return $this->query;
    }

    private function deleteQuery(): string
    {
        $this->query .= "{$this->table}";
        $this->whereBuilder();
        return $this->query;
    }

    final public function getValues(): array
    {
        return array_map(fn($data) => $data['value'], $this->data);
    }
}
