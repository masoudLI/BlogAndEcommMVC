<?php

namespace Tests\Blog\Repository;

use App\Blog\Model\Post;
use App\Blog\Repository\PostRepository;
use Framework\Exceptions\NoRecordException;
use Tests\DatabaseTestCase;

class PostRepositoryTest extends DatabaseTestCase
{


    public function setUp(): void
    {
        parent::setUp();
        $pdo = $this->getPDO();
        $this->migrateDatabase($pdo);
        $this->postRepository = new PostRepository($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postRepository->getPdo());
        $post = $this->postRepository->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord()
    {
        $this->expectException(NoRecordException::class);
        $this->postRepository->find(1);
    }

    public function testUpdate()
    {
        $this->seedDatabase($this->postRepository->getPdo());
        $this->postRepository->update(1, ['name' => 'massoud', 'slug' => 'demo']);
        $post = $this->postRepository->find(1);
        $this->assertEquals('massoud', $post->getName());
        $this->assertEquals('demo', $post->getSlug());
    }

    public function testCreate()
    {
        $this->postRepository->insert([
            'name' => 'massoud',
            'slug' => 'demo',
            'content' => 'salut'
        ]);
        $post = $this->postRepository->find(1);
        $this->assertEquals('massoud', $post->getName());
        $this->assertEquals('demo', $post->getSlug());
        $this->assertEquals('salut', $post->getContent());
    }

    public function testDelete()
    {
        $this->postRepository->insert(['name' => 'massoud', 'slug' => 'demo']);
        $this->postRepository->insert(['name' => 'massoud', 'slug' => 'demo']);
        $count = $this->pdo->query("SELECT COUNT(id) FROM posts")->fetchColumn();
        $this->assertEquals(2, $count);
        $this->postRepository->delete($this->pdo->lastInsertId());
        $this->assertEquals(1, $count);
    }
}
