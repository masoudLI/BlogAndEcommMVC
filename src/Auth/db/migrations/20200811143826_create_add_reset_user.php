<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAddResetUser extends AbstractMigration
{

    public function change(): void
    {
        $this->table('users')
            ->addColumn('password_reset', 'string', ['null' => true])
            ->addColumn('password_reset_at', 'datetime', ['null' => true])
            ->update();
    }
}
