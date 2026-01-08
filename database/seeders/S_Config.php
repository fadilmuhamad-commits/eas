<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class S_Config extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('config')->insert([
      'instance_name' => "Klinik Ambatu",
      'running_text' => "Selamat Datang di Klinik Ambatu Semoga Cepat Sembuh",
      'color1_id' => 1,
      'color2_id' => 1,
      'color3_id' => 1,
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now()
    ]);
  }
}
