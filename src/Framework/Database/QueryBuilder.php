<?php

namespace Framework\Database;

use App\Framework\Database\PaginatedQuery;
use Exception;
use Framework\Exceptions\NoRecordException;
use IteratorAggregate;
use Pagerfanta\Pagerfanta;
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

    private $joins = [];

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


    /**
     * join
     *
     * @param  mixed $table
     * @param  mixed $condition
     * @param  mixed $type
     * @return self
     */
    public function join(string $table, string $condition, string $type = "left"): self
    {
        $this->joins[$type][] = [$table, $condition];
        return $this;
    }


    public function setParams(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /* public function setMaxResult(int $length): self
    {
        $this->limit = $length;
        return $this;
    } */


    public function limit(int $length, int $offset = 0): self
    {
        $this->limit = "$offset, $length";
        return $this;
    }

    public function offset(int $offset): self
    {
        if ($this->limit === null) {
            throw new \Exception("On peut pas definir offset si limit n'est pas defini", 1);
        }
        $this->offset = $offset;
        return $this;
    }


    public function paginate(int $maxPerPage, int $currentPage = 1): Pagerfanta
    {
        $paginator = new PaginatedQuery($this);
        return (new Pagerfanta($paginator))
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($currentPage);
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

        if (!empty($this->joins)) {
            foreach ($this->joins as $type => $joins) {
                foreach ($joins as [$table, $condition]) {
                    $parts[] = \strtoupper($type) . " JOIN $table ON $condition";
                }
            }
        }

        if (!empty($this->where)) {
            $parts[] = "WHERE";
            $parts[] =  "(" . join(') AND (', $this->where) . ')';
        }

        if (!empty($this->orderBy)) {
            $parts[] = 'ORDER BY';
            $parts[] = join(', ', $this->orderBy);
        }
        if ($this->limit) {
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
            $this->execute()->fetchAll(\PDO::FETCH_ASSOC),
            $this->entity
        );
    }

    public function fetch()
    {
        $record = $this->execute()->fetch(PDO::FETCH_ASSOC);
        if ($record === false) {
            return false;
        }
        if ($this->entity) {
            return Hydrator::hydrate($record, $this->entity);
        }
        return $record;
    }

    /**
     * return un resulta ou un exeption
     */
    public function fetchOrFail()
    {
        $record = $this->fetch();
        if ($record === false) {
            throw new NoRecordException();
        }
        return $record;
    }

    /**
     * count
     *
     * @return void
     */
    public function count()
    {
        $query = clone $this;
        $table = current($this->from);
        return $query->select("COUNT($table.id)")->execute()->fetchColumn();
    }


    public function getIterator(): \Traversable
    {
        return $this->fetchAll();
    }
}
