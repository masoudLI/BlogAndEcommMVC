<?php

namespace App\Blog\Repository;

use App\Blog\Model\Post;
use Framework\Database\AbstractRepository;
use Framework\Database\QueryBuilder;

class PostRepository extends AbstractRepository
{

    protected string $table = 'posts';

    protected string $entity = Post::class;


    public function findAll(): QueryBuilder
    {
        $category = new CategoryRepository($this->pdo);
        return $this->makeQuery()
            ->select('p.*, c.name as categoryName, c.slug as categorySlug')
            ->join($category->getTable() . ' as c', 'c.id = p.category_id')
            ->orderBy('p.created_at', 'DESC');
    }

    public function findPaginatedPublic()
    {
        return $this->findAll()
            ->where('p.published = 1')
            ->where('p.created_at < NOW()');
    }


    public function findPaginatedPublicForCategory(int $categoryId)
    {
        return $this->findPaginatedPublic()
            ->where('p.category_id = :category')
            ->setParams('category', $categoryId);
    }


    public function findWithCategory(int $postId): Post
    {
        return $this->findPaginatedPublic()
            ->where('p.id = :id')
            ->setParams('id', $postId)
            ->fetch();
    }
}
