<?php

namespace Tests\Framework\Database;

use Tests\DatabaseTestCase;
use Framework\Database\AbstractRepository;
use PDO;
use ReflectionClass;

class AbstractRepositoryTest extends DatabaseTestCase
{

    public function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255)
        )');

        $this->table = new AbstractRepository($pdo);
        $reflextion = new ReflectionClass($this->table);
        $property = $reflextion->getProperty('table');
        $property->setAccessible(true);
        $property->setValue($this->table, 'test');
    }

    public function testExists()
    {
        $this->table->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->table->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertTrue($this->table->exists(1));
        $this->assertTrue($this->table->exists(2));
        $this->assertFalse($this->table->exists(3123));
    }
}
