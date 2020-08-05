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
        $query = (new QueryBuilder($this->getPDO()))
            ->from('posts', 'p')
            ->where('a = :a OR b = :b', 'c = :c');
        $query2 = (new QueryBuilder($this->getPDO()))
            ->from('posts', 'p')
            ->where('a = :a OR b = :b')
            ->where('c = :c');
        $this->assertEquals('SELECT * FROM posts as p WHERE (a = :a OR b = :b) AND (c = :c)', (string)$query);
        $this->assertEquals('SELECT * FROM posts as p WHERE (a = :a OR b = :b) AND (c = :c)', (string)$query2);
    }

    public function testLimitOrder()
    {
        $pdo = $this->getPDO();
        $query = (new QueryBuilder($pdo))
            ->from('posts', 'p')
            ->select('name')
            ->orderBy('id', 'DESC')
            ->orderBy('content', 'ASC')
            ->setMaxResult(5)
            ->offset(10);
        $this->assertEquals('SELECT name FROM posts as p ORDER BY id DESC, content ASC LIMIT 5 OFFSET 10', (string)$query);
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
        $posts = (new QueryBuilder($pdo))
            ->from('posts', 'p')
            ->into(Demo::class)
            ->fetchAll();
        $post = $posts[0];
        $post2 = $posts[0];
        $this->assertSame($post, $post2);
    }

    public function testjoin()
    {
        $pdo = $this->getPDO();
        $query = (new QueryBuilder($pdo))
            ->from('posts', 'p')
            ->select('name')
            ->join('categories as c', 'c.id = p.category_id')
            ->join('categories as c2', 'c2.id = p.category_id', 'inner');
        $this->assertEquals(
            'SELECT name FROM posts as p LEFT JOIN categories as c ON c.id = p.category_id INNER JOIN categories as c2 ON c2.id = p.category_id',
            (string)$query
        );
    }
}
