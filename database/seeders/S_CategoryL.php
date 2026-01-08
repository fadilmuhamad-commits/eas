<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class S_CategoryL extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('counter_categories')->insert([
      'id' => 1,
      'name' => "Umum",
      'code' => "UM",
      'color_id' => random_int(1, 30)
    ]);

    DB::table('counter_categories')->insert([
      'id' => 2,
      'name' => "Gigi",
      'code' => "GG",
      'color_id' => random_int(1, 30)
    ]);

    DB::table('counter_categories')->insert([
      'id' => 3,
      'name' => "Otot",
      'code' => "OT",
      'color_id' => random_int(1, 30)
    ]);

    DB::table('counter_categories')->insert([
      'id' => 4,
      'name' => "Bedah",
      'code' => "BD",
      'color_id' => random_int(1, 30)
    ]);

    DB::table('counter_categories')->insert([
      'id' => 5,
      'name' => "Mata",
      'code' => "MT",
      'color_id' => random_int(1, 30)
    ]);

    DB::table('counter_categories')->insert([
      'id' => 6,
      'name' => "Anak",
      'code' => "AN",
      'color_id' => random_int(1, 30)
    ]);

    DB::table('counter_categories')->insert([
      'id' => 7,
      'name' => "Urologi",
      'code' => "UR",
      'color_id' => random_int(1, 30)
    ]);

    DB::table('counter_categories')->insert([
      'id' => 8,
      'name' => "Kulit",
      'code' => "KU",
      'color_id' => random_int(1, 30)
    ]);
  }
}
