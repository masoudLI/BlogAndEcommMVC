<?php

namespace App\Framework\Database;

use Framework\Database\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private $query;

    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }
    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return $this->query->count();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Iterator|\IteratorAggregate The slice.
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->query;
        return $query->limit($length, $offset)->fetchAll();
    }
}
