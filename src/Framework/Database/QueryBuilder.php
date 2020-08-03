<?php

namespace Framework\Database;

use PDO;

class QueryBuilder
{


    private $select;

    private $where = [];

    private $params = [];

    private $from;

    private $limit;

    private $orderBy;

    private $offset;

    private $join;

    private $pdo;

    /**
     * @var null|string Entité à utiliser pour hydrater nos objets
     */
    private $entity;




    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function from(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->from[$table] = $alias;
        } else {
            $this->from[] = $table;
        }
        return $this;
    }


    public function into(string $entity): self
    {
        $this->entity = $entity;
        return $this;
    }


    public function select(string ...$conditions): self
    {
        $this->select = $conditions;
        return $this;
    }

    public function count()
    {
        $query = clone $this;
        $table = current($this->from);
        return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }


    public function where(string ...$condition): self
    {
        $this->where = array_merge($this->where, $condition);
        return $this;
    }

    public function orderBy(string $key, string $direction): self
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            $this->orderBy[] = $key;
        } else {
            $this->orderBy[] = "$key $direction";
        }
        return $this;
    }


    public function setParams(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function setMaxResult(int $int): self
    {
        $this->limit = $int;
        return $this;
    }

    public function offset(int $offset): self
    {
        if ($this->limit === null) {
            throw new \Exception("On peut pas definir offset si offset n'est pas defini", 1);
        }
        $this->offset = $offset;
        return $this;
    }


    public function __toString()
    {
        $parts = ['SELECT'];
        if ($this->select) {
            $parts[] = join(', ', $this->select);
        } else {
            $parts[] = "*";
        }

        $parts[] = "FROM";
        $parts[] = $this->builFrom();

        if (!empty($this->where)) {
            $parts[] = "WHERE";
            $parts[] =  "(" . join(') AND (', $this->where) . ')';
        }

        if (!empty($this->orderBy)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->orderBy);
        }
        if ($this->limit > 0) {
            $parts[] = "LIMIT " . $this->limit;
        }

        if ($this->offset !== null) {
            $parts[] = "OFFSET " . $this->offset;
        }

        return join(' ', $parts);
    }

    public function builFrom()
    {
        $from = [];
        foreach ($this->from as $key => $value) {
            if (is_string($key)) {
                $from[] = "$key as $value";
            } else {
                $from[] = "$value";
            }
        }
        return join(', ', $from);
    }


    public function execute()
    {
        $query = $this->__toString();
        if (!empty($this->params)) {
            $statement = $this->pdo->prepare($query);
            $statement->execute($this->params);
            return $statement;
        } else {
            return $this->pdo->query($query);
        }
    }

    public function fetchAll(): QueryResult
    {
        return new QueryResult(
            $this->execute()->fetchAll(PDO::FETCH_ASSOC),
            $this->entity
        );
    }
}
