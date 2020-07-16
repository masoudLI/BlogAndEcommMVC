<?php

namespace App\Blog\Repository;

use App\Blog\Model\Post;
use App\Framework\Database\PaginatedQuery;
use Framework\Database\AbstractRepository;
use Pagerfanta\Pagerfanta;

class PostRepository extends AbstractRepository
{

    protected string $table = 'posts';

    protected string $entity = Post::class;

    public function findPaginatedPublicForCategory(int $maxPerPage, int $currentPage, int $categoryId)
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name as categoryName, c.slug as categorySlug
                FROM {$this->table} as p
                LEFT JOIN categories as c ON p.category_id = c.id
                WHERE p.category_id = :category_id
                ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->table} WHERE category_id = :category_id",
            $this->entity,
            [
                'category_id' => $categoryId
            ]

        );
        return (new Pagerfanta($query))

            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($currentPage);
    }

    public function findPaginatedPublic(int $maxPerPage, int $currentPage)
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT p.*, c.name as categoryName, c.slug as categorySlug
                FROM {$this->table} as p
                LEFT JOIN categories as c ON p.category_id = c.id
                ORDER BY p.created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
            $this->entity
        );
        return (new Pagerfanta($query))

            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($currentPage);
    }


    protected function paginatedQuery()
    {
        return "SELECT p.id, p.name, p.created_at, c.name as category
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            ORDER BY created_at ASC";
    }
}
