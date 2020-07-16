<?php

namespace Framework\Database;

use \PDO;
use Pagerfanta\Pagerfanta;
use App\Framework\Database\PaginatedQuery;
use Framework\Exceptions\NoRecordException;

class AbstractRepository
{

    /**
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * Nom de la table en BDD
     * @var string
     */
    protected string $table;

    /**
     * Entité à utiliser
     * @var string|null
     */

    protected string $entity;



    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function findPaginated(int $maxPerPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->paginatedQuery(),
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($currentPage);
    }


    protected function paginatedQuery()
    {
        return "SELECT * FROM {$this->table}";
    }


    public function findList()
    {
        $results = $this->pdo->query("SELECT id, name FROM {$this->table}")
            ->fetchAll(PDO::FETCH_NUM);
        $lists = [];
        foreach ($results as $result) {
            $lists[$result[0]] = $result[1];
        }
        return $lists;
    }


    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT 10");
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetchAll();
    }

    public function find(int $id)
    {
        $record = $this->fetchOrFail("SELECT * FROM {$this->table} where id = :id", ['id' => $id]);
        if ($record === false) {
            throw new NoRecordException("{$this->table}", $id);
        }
        return $record;
    }

    public function update(int $id, array $params): bool
    {
        $filedQuery = $this->buildFieldQuery($params);
        $params['id'] = $id;
        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $filedQuery where id = :id");
        return $statement->execute($params);
    }

    public function insert(array $params): bool
    {
        $fields = array_keys($params);
        $values = join(', ', array_map(function ($field) {
            return ':' . "$field";
        }, $fields));
        $fields = join(', ', $fields);
        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ($fields) VALUES ($values)");
        return $statement->execute($params);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM $this->table where id = :id");
        return $statement->execute(['id' => $id]);
    }

    public function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity) {
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $query->setFetchMode(PDO::FETCH_OBJ);
        }
        return $query->fetch();
    }


    private function buildFieldQuery(array $params)
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
    }

    /**
     * Vérifie qu'un enregistrement existe
     *
     * @param  int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        $stat = $this->pdo->prepare("SELECT id FROM {$this->table} where id = :id");
        $stat->execute(['id' => $id]);
        return $stat->fetchColumn() !== false;
    }

    
    /**
     * getPdo
     *
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Get the value of entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get nom de la table en BDD
     *
     * @return  string
     */
    public function getTable()
    {
        return $this->table;
    }
}
