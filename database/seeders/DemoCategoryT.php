<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoCategoryT extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('ticket_categories')->insert([
      'id' => 1,
      'name' => "KiosK",
      'code' => "A"
    ]);

    DB::table('ticket_categories')->insert([
      'id' => 2,
      'name' => "Booking",
      'code' => "B"
    ]);

    DB::table('ticket_categories')->insert([
      'id' => 3,
      'name' => "Emergency",
      'code' => "C"
    ]);
  }
}
