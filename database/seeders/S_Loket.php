<?php

namespace Database\Seeders;

use App\Models\M_Counter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class S_Loket extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $counter1 = M_Counter::create([
      'id' => 1,
      'name' => 'Loket 1',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter1->Categories()->attach(random_int(1, 8));

    $counter2 = M_Counter::create([
      'id' => 2,
      'name' => 'Loket 2',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter2->Categories()->attach(random_int(1, 8));

    $counter3 = M_Counter::create([
      'id' => 3,
      'name' => 'Loket 3',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter3->Categories()->attach(random_int(1, 8));

    $counter4 = M_Counter::create([
      'id' => 4,
      'name' => 'Loket 4',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter4->Categories()->attach(random_int(1, 8));

    $counter5 = M_Counter::create([
      'id' => 5,
      'name' => 'Loket 5',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter5->Categories()->attach(random_int(1, 8));

    $counter6 = M_Counter::create([
      'id' => 6,
      'name' => 'Loket 6',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter6->Categories()->attach(random_int(1, 8));

    $counter7 = M_Counter::create([
      'id' => 7,
      'name' => 'Loket 7',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter7->Categories()->attach(random_int(1, 8));

    $counter8 = M_Counter::create([
      'id' => 8,
      'name' => 'Loket 8',
      'status' => random_int(1, 2),
      'color_id' => random_int(1, 30),
      'group_id' => random_int(1, 10)
    ]);
    $counter8->Categories()->attach(random_int(1, 8));
  }
}
