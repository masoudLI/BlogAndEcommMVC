<?php

namespace App\Shop\Repository;

use App\Shop\Model\Shop;
use Framework\Database\AbstractRepository;

class ShopRepository extends AbstractRepository
{

    protected string $table = 'products';

    protected string $entity = Shop::class;
}
