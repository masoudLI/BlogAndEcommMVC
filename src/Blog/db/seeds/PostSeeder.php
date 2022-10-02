<?php


use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{

    public function run()
    {
        $faker = Faker\Factory::create();
    

        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'name'          => $faker->name,
                'slug'          => $faker->slug,
                'content'       => $faker->text(3000),
                'created_at'    => date('Y-m-d H:i:s', $faker->unixTime('now')),
                'updated_at'    => date('Y-m-d H:i:s', $faker->unixTime('now')),
                //'category_id'   => rand(1, 5),
                'image' => 'fake.jpg',
                'published' => 1
            ];
        }
        $this->table('posts')->insert($data)->saveData();
    }
}
