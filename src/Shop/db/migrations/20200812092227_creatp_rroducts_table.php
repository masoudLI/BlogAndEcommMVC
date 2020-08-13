<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatpRroductsTable extends AbstractMigration
{

    public function change(): void
    {
        $this->table('products')
            ->addColumn('title', 'string')
            ->addColumn('slug', 'string')
            ->addColumn('description', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->addColumn('image', 'string')
            ->addColumn('price', 'float', ['precision' => 6, 'scale' => 2])
            ->addColumn('updated_at', 'datetime')
            ->addColumn('created_at', 'datetime')
            ->addIndex('slug', ['unique' => true])
            ->create();
    }
}
