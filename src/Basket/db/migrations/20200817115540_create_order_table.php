<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOrderTable extends AbstractMigration
{

    public function change(): void
    {
        $constraints = ['delete' => 'cascade'];
        $this->table('orders')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id')
            ->addColumn('price', 'float', ['precision' => 6, 'scale' => 2])
            ->addColumn('vat', 'float', ['precision' => 6, 'scale' => 2])
            ->addColumn('country', 'string')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addColumn('charge_id', 'string')
            ->addIndex(['charge_id'], ['unique' => true])
            ->create();

        $this->table('orders_products')
            ->addColumn('order_id', 'integer')
            ->addColumn('product_id', 'integer')
            ->addColumn('quantity', 'integer')
            ->addColumn('price', 'float', ['precision' => 10, 'scale' => 2])
            ->addForeignKey('order_id', 'orders', 'id', $constraints)
            ->addForeignKey('product_id', 'products', 'id', $constraints)
            ->create();
    }
}
