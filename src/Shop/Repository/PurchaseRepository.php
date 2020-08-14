<?php

namespace App\Shop\Repository;

use App\Auth\Model\User;
use App\Shop\Model\Purchase;
use App\Shop\Model\Product;
use Framework\Database\AbstractRepository;
use Framework\Database\QueryResult;

class PurchaseRepository extends AbstractRepository
{

    protected string $table = 'purchases';

    protected string $entity = Purchase::class;


    public function findForAlreadyPurchase(Product $product, User $user): ?Purchase
    {

        return $this->makeQuery()
            ->where('product_id = :product')
            ->where('user_id = :user')
            ->setParams('product', $product->getId())
            ->setParams('user', $user->getId())
            ->fetch() ?: null;
    }


    public function findForUsePurchase(User $user): QueryResult
    {
        return $this->makeQuery()
            ->select('p.*, pr.title as productName')
            ->where('p.user_id = :user')
            ->join('products as pr', 'p.product_id = pr.id')
            ->setParams('user', $user->getId())
            ->fetchAll();

    }

    public function findWithProduct(int $purchaseId): Purchase
    {
        return $this->makeQuery()
            ->select('p.*, pr.title as productName')
            ->where('p.id = :purchase')
            ->join('products as pr', 'p.product_id = pr.id')
            ->setParams('purchase', $purchaseId)
            ->fetchOrFail();
    }
}
