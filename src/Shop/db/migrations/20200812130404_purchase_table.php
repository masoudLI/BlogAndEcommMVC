<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PurchaseTable extends AbstractMigration
{

    public function change(): void
    {
        $this->table('purchases')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id')
            ->addColumn('product_id', 'integer')
            ->addForeignKey('product_id', 'products', 'id')
            ->addColumn('price', 'float', ['precision' => 6, 'scale' => 2])
            ->addColumn('vat', 'float', ['precision' => 6, 'scale' => 2])
            ->addColumn('country', 'string')
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addColumn('charge_id', 'string')
            ->addIndex(['charge_id'], ['unique' => true])
            ->create();
    }
}
