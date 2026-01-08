<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoConfig extends Seeder
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

    DB::table('config')->insert([
      'running_text' => "Selamat Datang",
      'color1_id' => 1,
      'color2_id' => 1,
      'color3_id' => 1,
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now()
    ]);
  }
}
