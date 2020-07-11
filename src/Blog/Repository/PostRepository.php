<?php

namespace App\Blog\Repository;

use App\Blog\Model\Post;
use Framework\Database\AbstractRepository;

class PostRepository extends AbstractRepository
{

    protected string $table = 'posts';

    protected string $entity = Post::class;


    public function findAllOrderLimit () {
        return $this->findAll("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT 10");
    }

}
