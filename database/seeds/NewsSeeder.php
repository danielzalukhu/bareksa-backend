<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $date = new DateTime();

    	for($i = 1; $i <= 1000; $i++){

    		DB::table('news')->insert([
    			'topic_id' => 2,
    			'title' => $faker->sentence($nbWords = 6, $variableNbWords = true),
    			'thumbnail' => 'images/' . Str::random(5) . '.jpg',
    			'content' => $faker->text($maxNbChars = 200),
                'status' => 'deleted',
                'created_by' => $faker->name,
                'created_at' => now()
    		]);

    	}
    }
}
