<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call([
      S_Color::class,
      S_CategoryL::class,
      S_CategoryT::class,
      S_Config::class,
      S_Pengunjung::class,
      S_Group::class,
      S_Loket::class,
      S_Permission::class,
      S_Role::class,
      S_User::class,
      S_Tiket::class,

      // DemoConfig::class,
      // S_Permission::class,
      // DemoRole::class,
      // DemoUser::class,
      // DemoCategoryT::class,
    ]);
  }
}
