<?php

namespace App\Basket\Repository;

use App\Basket\BasketRow;
use Framework\Database\AbstractRepository;

class BasketRowRepository extends AbstractRepository
{

    protected string $table = 'baskets_products';

    protected string $entity = BasketRow::class;
}
