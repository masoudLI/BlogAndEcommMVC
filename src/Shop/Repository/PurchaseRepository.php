<?php

namespace App\Shop\Repository;

use App\Auth\Model\User;
use App\Shop\Model\Purchase;
use App\Shop\Model\Product;
use Framework\Database\AbstractRepository;

class PurchaseRepository extends AbstractRepository
{

    protected string $table = 'purchase';

    protected string $entity = Purchase::class;


    public function findForAlreadyPurchase(Product $product, User $user): ?Purchase
    {

        return $this->makeQuery()
            ->where('product_id = :product')
            ->where('user_id = :user')
            ->setParams('product', $product->getId())
            ->setParams('user', $user->getId())
            ->fetch();
    }
}
