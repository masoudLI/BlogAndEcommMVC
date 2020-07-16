<?php

namespace App\Blog\Repository;

use App\Blog\Model\Category;
use Framework\Database\AbstractRepository;

class CategoryRepository extends AbstractRepository
{

    protected string $table = 'categories';

    protected string $entity = Category::class;

}
