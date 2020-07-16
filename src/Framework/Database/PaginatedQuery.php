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
    private array $params;

    public function __construct(PDO $pdo, string $query, string $countQuery, $entity, array $params = [])
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $countQuery;
        $this->entity = $entity;
        $this->params = $params;
    }
    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    function getNbResults()
    {
        if (!empty($this->params)) {
            $stat = $this->pdo->prepare($this->countQuery);
            $stat->execute($this->params);
            return $stat->fetchColumn();
        }
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
    function getSlice($offset, $length)
    {
        $statement = $this->pdo->prepare($this->query . ' LIMIT :offset, :length');
        foreach ($this->params as $key => $param) {
            $statement->bindParam($key, $param);
        }
        $statement->bindParam('offset', $offset, PDO::PARAM_INT);
        $statement->bindParam('length', $length, PDO::PARAM_INT);
        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        $statement->execute();
        return $statement->fetchAll();
    }
}
