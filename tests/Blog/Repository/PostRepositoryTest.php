<?php

namespace Tests\Blog\Repository;

use App\Blog\Model\Post;
use App\Blog\Repository\PostRepository;
use Framework\Exceptions\NoRecordException;
use Tests\DatabaseTestCase;

class PostRepositoryTest extends DatabaseTestCase
{

    /**
     * @var PostTable
     */
    private $postTable;

    public function setUp(): void
    {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostRepository($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(55);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord()
    {
        $this->expectException(NoRecordException::class);
        $this->postTable->find(1);
    }

    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $this->postTable->update(1, ['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut', $post->getName());
        $this->assertEquals('demo', $post->getSlug());
    }

    public function testInsert()
    {
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('Salut', $post->getName());
        $this->assertEquals('demo', $post->getSlug());
    }

    public function testDelete()
    {
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $this->postTable->insert(['name' => 'Salut', 'slug' => 'demo']);
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int) $count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int)$count);
    }
}
