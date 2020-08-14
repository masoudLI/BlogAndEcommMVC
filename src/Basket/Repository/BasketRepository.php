<?php

namespace App\Basket\Repository;

use App\Basket\Basket;
use App\Shop\Model\Product;
use App\Shop\Repository\ProductRepository;
use Framework\Database\AbstractRepository;
use Framework\Database\QueryBuilder;

class BasketRepository extends AbstractRepository
{

    public function __construct(\PDO $pdo)
    {
        $this->basketRepository = new ProductRepository($pdo);
    }

    public function hydrateBasket(Basket $basket)
    {
        $rows = $basket->getRows();
        if (empty($rows)) {
            return null;
        }
        $ids = array_map(function ($row) {
            return $row->getProductId();
        }, $rows);

        $products = $this->basketRepository->makeQuery()
            ->where('id IN (' . implode(',', $ids) . ')')
            ->fetchAll();

        $productsById = [];

        foreach ($products as $product) {
            $productsById[$product->getId()] = $product;
        }

        foreach ($rows as $row) {
            $row->setProduct($productsById[$row->getProductId()]);
        }
    }
}
