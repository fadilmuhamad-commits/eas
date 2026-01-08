<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class S_Pengunjung extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $faker = Faker::create('id_ID');
    foreach (range(1, 100) as $value) {
      $randomDate = $faker->dateTimeBetween('2024-01-01', '2024-12-31')->format('Y-m-d');

      DB::table('customers')->insert([
        'name' => $faker->name(),
        'registration_code' => rand(100000000, 999999999),
        'email' => $faker->email(),
        'phone_number' => $faker->phoneNumber(),
        'address' => $faker->address(),
        'birth_place' => $faker->city(),
        'birth_date' => $faker->date(),
        'created_at' => $faker->dateTimeThisMonth(),
        'updated_at' => $faker->dateTimeThisMonth()
      ]);
    }
  }
}
