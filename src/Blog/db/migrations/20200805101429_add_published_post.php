<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddPublishedPost extends AbstractMigration
{
    public function change(): void
    {
        $this->table('posts')
            ->addColumn('published', 'boolean', ['default' => true])
            ->update();
    }
}
