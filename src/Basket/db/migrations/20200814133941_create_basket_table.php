<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBasketTable extends AbstractMigration
{

    public function change(): void
    {
        $constraints = ['delete' => 'cascade'];
        $this->table('baskets')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id', $constraints)
            ->create();

        $this->table('baskets_products')
            ->addColumn('basket_id', 'integer')
            ->addColumn('product_id', 'integer')
            ->addForeignKey('basket_id', 'baskets', 'id', $constraints)
            ->addForeignKey('product_id', 'products', 'id', $constraints)
            ->addColumn('quantity', 'integer')
            ->create();
    }
}
