<?php

namespace App\Blog\Repository;

use App\Blog\Model\Post;
use Framework\Database\AbstractRepository;

class PostRepository extends AbstractRepository
{

    protected string $table = 'posts';

    protected string $entity = Post::class;


    protected function findPaginatedQuerylimit()
    {
        return " ORDER BY created_at DESC";
    }
}
