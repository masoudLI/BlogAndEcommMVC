<?php

namespace App\Shop\Repository;

use App\Shop\Model\Product;
use Framework\Database\AbstractRepository;
use Framework\Database\QueryBuilder;

class ProductRepository extends AbstractRepository
{

    protected string $table = 'products';

    protected string $entity = Product::class;

    public function findPublic(): QueryBuilder
    {
        return $this->makeQuery()
            ->where('created_at < NOW()');
    }
}
