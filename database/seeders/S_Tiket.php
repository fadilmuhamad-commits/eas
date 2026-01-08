<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class S_Tiket extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $faker = Faker::create('id_ID');
    $booking = 242501;
    $antrian = 101;
    $pengunjung = 1;
    $i = 1;
    foreach (range(1, 100) as $value) {
      // $randomDate = $faker->dateTimeBetween('2024-01-01', '2024-12-31')->format('Y-m-d');

      $status = rand(1, 2) === 1 ? rand(1, 2) : 4;
      $categoryT = random_int(1, 2);
      $loket_id = ($status == 1) ? random_int(1, 8) : null;
      if ($status == 4) {
        $no_booking = $booking++;
      } else {
        $no_booking = ($categoryT == 2) ? $booking++ : null;
      }
      $position = ($status == 2 || $status == 4) ? $i++ : null;
      $duration = ($status == 1) ? random_int(1800, 7200) : null;

      DB::table('tickets')->insert([
        'booking_code' => $no_booking,
        'queue_number' => $antrian++,
        'customer_id' => $pengunjung++,
        'counter_category_id' => random_int(1, 8),
        'ticket_category_id' => $categoryT,
        'position' => $position,
        'status' => $status,
        'counter_id' => $loket_id,
        'duration' => $duration,
        'created_at' => $faker->dateTimeThisMonth(),
        'updated_at' => $faker->dateTimeThisMonth()
      ]);
    }
  }
}
