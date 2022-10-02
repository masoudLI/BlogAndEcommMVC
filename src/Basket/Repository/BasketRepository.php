<?php

namespace App\Basket\Repository;

use App\Basket\Basket;
use App\Basket\BasketRow;
use App\Basket\Model\Basket as BasketModel;
use App\Shop\Model\Product;
use App\Shop\Repository\ProductRepository;
use Framework\Database\AbstractRepository;
use Framework\Database\Hydrator;

class BasketRepository extends AbstractRepository
{
    protected string $table = 'baskets';

    protected string $entity = BasketModel::class;

    public function __construct(\PDO $pdo)
    {
        $this->productRepository = new ProductRepository($pdo);
        $this->basketRowRepository = new BasketRowRepository($pdo);
        parent::__construct($pdo);
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

        $products = $this->productRepository->makeQuery()
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

    public function findUserForBasket(int $userId)
    {
        return $this->makeQuery()
            ->where('user_id = :userId')
            ->setParams('userId', $userId)
            ->fetch() ?: null;
    }

    public function createUserForBasket(int $userId): BasketModel
    {
        $params = [
            'user_id' => $userId
        ];
        $this->insert($params);
        $params['id'] = $this->getPdo()->lastInsertId();
        return Hydrator::hydrate($params, $this->entity);
    }

    public function addRow(BasketModel $basket, Product $product, int $quantity): BasketRow
    {
        $params = [
            'basket_id' => $basket->getId(),
            'product_id' => $product->getId(),
            'quantity' => $quantity
        ];
        $params['id'] = $this->getPdo()->lastInsertId();
        $this->basketRowRepository->insert($params);
        $row = Hydrator::hydrate($params, $this->basketRowRepository->getEntity());
        $row->setProduct($product);
        return $row;
    }

    public function updateRowQuantity(BasketRow $basketRow, int $quantity): BasketRow
    {
        $this->basketRowRepository->update($basketRow->getId(), ['quantity' => $quantity]);
        $basketRow->setQuantity($quantity);
        return $basketRow;
    }


    public function deleteRow(BasketRow $basketRow): void
    {
        /** @var BasketRow */
        $this->basketRowRepository->delete($basketRow->getId());
    }


    public function findRows(BasketModel $modelBasket): array
    {
        return $this->basketRowRepository
            ->makeQuery()
            ->where("basket_id = :basketId")
            ->setParams('basketId', $modelBasket->getId())
            ->fetchAll()
            ->toArray();
    }

    public function deleteRows(BasketModel $modelBasket)
    {
        return $this->pdo->exec('DELETE FROM  baskets_products WHERE basket_id = ' . $modelBasket->getId());
    }
}
