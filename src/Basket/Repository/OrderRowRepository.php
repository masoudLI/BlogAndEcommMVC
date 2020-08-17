<?php

namespace App\Basket\Repository;

use App\Basket\Model\OrderRow;
use Framework\Database\AbstractRepository;

class OrderRowRepository extends AbstractRepository
{

    protected string $table = 'orders_products';

    protected string $entity = OrderRow::class;
}
