<?php


use Phinx\Seed\AbstractSeed;

class ProductSeeder extends AbstractSeed
{

    public function run()
    {
        $data = [];
        $faker = Faker\Factory::create('fr_FR');
        $date = $faker->unixTime('now');
        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'title' => $faker->title,
                'slug' => $faker->slug,
                'price' => $faker->numberBetween(100, 1000),
                'description' => $faker->text(3000),
                'image' => 'fake.jpg',
                'created_at' => $faker->date('Y-m-d H:i:s', $date),
                'updated_at' => $faker->date('Y-m-d H:i:s', $date)
            ];
        }

        $this->table('products')
            ->insert($data)
            ->save();
    }
}
