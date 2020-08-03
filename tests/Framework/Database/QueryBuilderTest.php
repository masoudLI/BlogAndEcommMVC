<?php

namespace Tests\Framework\Database;

use Framework\Database\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Tests\DatabaseTestCase;

class QueryBuilderTest extends DatabaseTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = new QueryBuilder($this->getPDO());
    }



    public function testFromSimpleQuery()
    {
        $query = (new QueryBuilder($this->getPDO()))
            ->from('posts', 'p')
            ->select('*');
        $this->assertEquals("SELECT * FROM posts as p", (string)$query);
    }

    public function testWhereQuery()
    {
        $query = (new QueryBuilder($this->getPDO()))
            ->from('posts', 'p')
            ->where('a = :a OR b = :b')
            ->where('c = :c')
            ->select('*');
        $this->assertEquals("SELECT * FROM posts as p WHERE (a = :a OR b = :b) AND (c = :c)", (string)$query);
    }

    public function testWhereParamsQuery()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = (new QueryBuilder($pdo))
            ->from('posts', 'p')
            ->count();
        $this->assertEquals(100, $posts);
        $posts = (new QueryBuilder($pdo))
            ->from('posts', 'p')
            ->where('p.id < :number')
            ->where('p.name < :name')
            ->setParams('number', 30)
            ->setParams('name', 'saeed')
            ->count();
        $this->assertEquals(29, $posts);
    }

    public function testLimitOrder()
    {
        $query = (new QueryBuilder($this->getPDO()))
            ->from('posts', 'p')
            ->select('name')
            ->orderBy('id', 'DESC')
            ->orderBy('name', 'ASC')
            ->setMaxResult(5)
            ->offset(10);
        $this->assertEquals('SELECT name FROM posts as p ORDER BY id DESC, name ASC LIMIT 5 OFFSET 10', (string)$query);
    }

    public function testHydrateEntity()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = (new QueryBuilder($pdo))
            ->from('posts', 'p')
            ->into(Demo::class)
            ->fetchAll();
        $this->assertEquals('demo', substr($posts[0]->getSlug(), -4));
    }

    public function testLazyHydrate()
    {
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->seedDatabase($pdo);
        $posts = (new QueryBuilder($pdo))
            ->from('posts', 'p')
            ->into(Demo::class)
            ->fetchAll();
        $post = $posts[0];
        $post2 = $posts[0];
        $this->assertSame($post, $post2);
    }
}
