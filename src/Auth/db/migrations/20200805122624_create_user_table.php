<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration
{

    public function change(): void
    {
        $this->table('users')
            ->addColumn('username', 'string')
            ->addColumn('email', 'string')
            ->addColumn('password', 'string')
            ->addColumn('role', 'string')
            ->addIndex(['email', 'username'], ['unique' => true])
            ->create();
    }
}
