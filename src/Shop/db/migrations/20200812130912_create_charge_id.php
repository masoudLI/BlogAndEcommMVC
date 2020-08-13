<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateChargeId extends AbstractMigration
{

    public function change(): void
    {
        $this->table('users_stripe')
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'users', 'id')
            ->addColumn('customer_id', 'string')
            ->addColumn('created_at', 'datetime')
            ->create();
    }
}
