<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class S_Color extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('colors')->insert([
      'id' => 1,
      'hexcode' => "#214234"
    ]);

    $faker = Faker::create();
    foreach (range(2, 30) as $value) {
      DB::table('colors')->insert([
        'id' => $value,
        'hexcode' => $faker->hexColor()
      ]);
    }
  }
}
