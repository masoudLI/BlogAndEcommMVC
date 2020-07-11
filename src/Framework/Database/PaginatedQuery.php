<?php

namespace App\Framework\Database;

use Pagerfanta\Adapter\AdapterInterface;
use PDO;

class PaginatedQuery implements AdapterInterface
{
    private PDO $pdo;
    private string $query;
    private string $countQuery;
    private string $entity;

    public function __construct(PDO $pdo, string $query, string $countQuery, $entity)
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
    }
    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    function getNbResults() {
        return (int)$this->pdo->query($this->countQuery)->fetchColumn();
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Iterator|\IteratorAggregate The slice.
     */
    function getSlice($offset, $length) {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        $statement->bindParam('offset', $offset, PDO::PARAM_INT);
        $statement->bindParam('length', $length, PDO::PARAM_INT);
        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
}
