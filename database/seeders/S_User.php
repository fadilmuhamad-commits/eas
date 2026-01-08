<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class S_User extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  { {
      $faker = Faker::create('id_ID');
      DB::table('users')->insert([
        'name' => "Alpuket Buah Superadmin",
        'username' => "admin",
        'email' => "alpuket@gmail.com",
        'password' => Hash::make('0'),
        'role_id' => 1
      ]);
      DB::table('users')->insert([
        'name' => "Alpuket Buah Admin",
        'username' => "admin2",
        'email' => "alpuket2@gmail.com",
        'password' => Hash::make('0'),
        'role_id' => 2
      ]);
      DB::table('users')->insert([
        'name' => "Alpuket Buah Support",
        'username' => "admin3",
        'email' => "alpuket3@gmail.com",
        'password' => Hash::make('0'),
        'role_id' => 3
      ]);
      DB::table('users')->insert([
        'name' => "Alpuket Buah CS",
        'username' => "admin4",
        'email' => "alpuket4@gmail.com",
        'password' => Hash::make('0'),
        'role_id' => 4,
        'counter_id' => 1
      ]);
    }
  }
}
