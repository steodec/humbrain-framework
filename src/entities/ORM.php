<?php
/**
 * Copyright (c) 2023.
 * Humbrain All right reserved.
 **/

namespace Humbrain\Framework\entities;

use pdo;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * @author  Paul Tedesco <paul.tedesco@humbrain.com>
 * @version Release: 1.0.0
 */
trait ORM
{
    private pdo $pdo;
    private string $query = "";
    private array $values = [];

    /**
     * @var bool
     */
    private bool $customFrom = false;


    public function __construct(pdo $pdo)
    {
        $this->pdo = $pdo;
    }//end __construct()

    /**
     * Add select fields
     *
     * @param string $fields
     *
     * @return self
     */
    final public function addFields(string $fields = "*"): self
    {
        if (str_ends_with($this->query, "SELECT")) :
            $this->query .= " " . $fields;
        else :
            $this->query .= ", " . $fields;
        endif;
        return $this;
    }

    /**
     * Add Order by case in query select
     *
     * @param string $property
     * @param string $descOrAsc
     *
     * @return self
     */
    final public function addOrderBy(string $property, string $descOrAsc = "ASC"): self
    {
        $this->query = sprintf(" ORDER BY %s %s", $property, $descOrAsc);
        return $this;
    }

    /**
     * Save entity in database insert or update
     *
     * @return self
     * @throws ORMException
     */
    public function save(): self
    {
        $query = "";
        if (is_null($this->getId())) :
            $query = sprintf("INSERT INTO %s", $this::TABLE_NAME);
        else :
            $query = sprintf("UPDATE %s", $this::TABLE_NAME);
        endif;
        $reflectionClass = new ReflectionClass($this);
        $properties = [];
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $property) :
            if (!$property->isInitialized($this)) {
                continue;
            }
            $properties[$property->getName()] = $property->getValue($this);
        endforeach;
        $query .= $this->formatSet($properties);
        $queryPdo = $this->pdo->prepare($query);
        foreach ($properties as $key => $property) :
            $queryPdo->bindParam(":$key", $property, $this->getType($property));
        endforeach;
        $execute = $queryPdo->execute();
        if (!$execute) :
            throw new ORMException("Une erreur c'est produite lors de l'execution");
        endif;
        if (is_null($this->getId())) :
            $this->id = $this->pdo->lastInsertId($this::TABLE_NAME);
        endif;
        $result = $this->select()
            ->addWhereCase('id', OperatorEnum::EQUAL, $this->getId())
            ->addLimit(1)
            ->execute();
        return $result;
    }

    /**
     * Return set input like SET key1=:key1, key2=:key2
     *
     * @param array $properties
     *
     * @return string
     */
    private function formatSet(array $properties): string
    {
        $set = "";
        foreach ($properties as $key => $property) :
            if (array_key_first($properties, $key)) :
                $set .= "SET";
            endif;
            $set .= " $key = :$key";
            if (!array_key_last($properties, $key)) :
                $set .= ",";
            endif;
        endforeach;
        return $set;
    }

    /**
     * Return pdo type for bindParam
     *
     * @param mixed $value
     *
     * @return int
     * @throws ORMException
     */
    private function getType(mixed $value): int
    {
        return match (gettype($value)) {
            "boolean" => pdo::PARAM_BOOL,
            "double", "integer" => pdo::PARAM_INT,
            "array", "object", "string" => pdo::PARAM_STR,
            'NULL' => pdo::PARAM_NULL,
            default => throw new ORMException('Unexpected value'),
        };
    }

    /**
     * Execute select query
     *
     * @param string|null $query
     *
     * @return iterable|self|object
     * @throws ORMException
     */
    final public function execute(string $query = null): iterable|object
    {
        if (is_null($query)) :
            $querypdo = $this->pdo->prepare($this->query);
            foreach ($this->values as $key => $value) :
                $querypdo->bindParam(":$key", $value, $this->getType($value));
            endforeach;
        else :
            $querypdo = $this->pdo->query($query);
        endif;
        $result = $querypdo->execute();
        if (!$result) :
            throw new ORMException("Une erreur c'est produite");
        endif;
        if ($this->customFrom) :
            $return = $querypdo->fetchAll(pdo::FETCH_OBJ);
        else :
            $return = $querypdo->fetchAll(pdo::FETCH_CLASS, $this::class);
        endif;

        return (count($return) > 0) ?
            (count($return) > 1) ? $return : $return[0]
            : throw new ORMException("Aucune entrée");
    }

    /**
     * Add Limit in query select
     *
     * @param int $limit
     *
     * @return self
     */
    final public function addLimit(int $limit): self
    {
        $this->query .= " LIMIT " . $limit;
        return $this;
    }

    /**
     * Add where case
     *
     * @param string $property
     * @param OperatorEnum $operatorEnum
     * @param mixed $value
     * @param string $operator
     *
     * @return self
     */
    final public function addWhereCase(
        string       $property,
        OperatorEnum $operatorEnum,
        mixed        $value,
        string       $operator = "AND"
    ): self
    {
        if (!str_contains($this->query, 'FROM')) :
            $this->query .= sprintf(" FROM %s", $this::TABLE_NAME);
        endif;
        if (str_ends_with($this->query, $this::TABLE_NAME)) :
            $this->query .= sprintf(" WHERE %s%s:%s", $property, $operatorEnum->value, $property);
        else :
            $this->query .= sprintf(" %s %s%s:%s", $operator, $property, $operatorEnum->value, $property);
        endif;
        $this->values[$property] = $value;
        return $this;
    }

    /**
     * Start query with select
     *
     * @return self
     */
    final public function select(): self
    {
        $this->query .= "SELECT";
        return $this;
    }

    /**
     * @param string $from
     * @return self
     */
    final public function addCustomFrom(string $from): self
    {
        $this->customFrom = true;
        $this->query .= "FROM " . $from;
        return $this;
    }
}
