<?php


use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{

    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'name'          => $faker->name,
                'slug'          => $faker->slug,
                'content'       => $faker->text(3000),
                'created_at'    => date('Y-m-d H:i:s', $faker->unixTime('now')),
                'updated_at'    => date('Y-m-d H:i:s', $faker->unixTime('now'))
            ];
        }
        $this->table('posts')->insert($data)->saveData();
    }
}
