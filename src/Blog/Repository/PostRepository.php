<?php

namespace App\Blog\Repository;

use App\Blog\Model\Post;
use Framework\Database\AbstractRepository;

class PostRepository extends AbstractRepository
{

    protected string $table = 'posts';

    protected string $entity = Post::class;


    protected function paginatedQuery()
    {

        return "SELECT p.id, p.name, p.created_at, c.name as category
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            ORDER BY created_at ASC";
    }
}
