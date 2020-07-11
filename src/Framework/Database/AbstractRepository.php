<?php

namespace Framework\Database;

use Framework\Exceptions\NoRecordException;
use \PDO;

abstract class AbstractRepository
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
}
