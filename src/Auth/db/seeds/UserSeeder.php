<?php


use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
   
    public function run()
    {
        $table = $this->table('users');
        $data = $table->insert([
            'username' => 'saeed',
            'email' => 'saeedemami@yahoo.fr',
            'password' => password_hash('saeed', PASSWORD_DEFAULT)
        ]);
        $data = $table->insert([
            'username' => 'massoud',
            'email' => 'massoudemami80@yahoo.fr',
            'password' => password_hash('massoud', PASSWORD_DEFAULT),
        ]);
        $table->save($data);
    }
}
