<?php

namespace Database\Seeders;

use App\Models\M_Permission;
use App\Models\M_Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class S_Role extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $permissions = M_Permission::get();

    $superadmin = M_Role::create([
      'id' => 1,
      'name' => "Superadmin",
    ]);
    foreach ($permissions as $permission) {
      $superadmin->Permissions()->attach($permission->id);
    }

    $admin = M_Role::create([
      'id' => 2,
      'name' => "Admin",
    ]);
    foreach ($permissions as $permission) {
      $admin->Permissions()->attach($permission->id);
    }

    $support = M_Role::create([
      'id' => 3,
      'name' => "Support",
    ]);
    foreach ($permissions as $permission) {
      $permission->name == 'get_ticket' ? $support->Permissions()->attach($permission->id) : '';
    }

    $customerservice = M_Role::create([
      'id' => 4,
      'name' => "CS",
    ]);
    foreach ($permissions as $permission) {
      $permission->name == 'view_queue' ? $customerservice->Permissions()->attach($permission->id) : '';
      $permission->name == 'manage_queue' ? $customerservice->Permissions()->attach($permission->id) : '';
      $permission->name == 'view_booking' ? $customerservice->Permissions()->attach($permission->id) : '';
      $permission->name == 'manage_booking' ? $customerservice->Permissions()->attach($permission->id) : '';
      $permission->name == 'view_customer' ? $customerservice->Permissions()->attach($permission->id) : '';
      $permission->name == 'view_history' ? $customerservice->Permissions()->attach($permission->id) : '';
      $permission->name == 'call_queue' ? $customerservice->Permissions()->attach($permission->id) : '';
    }

    M_Role::create([
      'id' => 5,
      'name' => "Teller"
    ]);

    // DB::table('role')->insert([
    //     'id' => 3,
    //     'name' => "CS Poliklinik Umum",
    //     'loket_id' => 1
    // ]);

    // DB::table('role')->insert([
    //     'id' => 4,
    //     'name' => "CS Poliklinik Gigi",
    //     'loket_id' => 2
    // ]);

    // DB::table('role')->insert([
    //     'id' => 5,
    //     'name' => "CS Poliklinik Otot",
    //     'loket_id' => 3
    // ]);

    // DB::table('role')->insert([
    //     'id' => 6,
    //     'name' => "CS Poliklinik Bedah",
    //     'loket_id' => 4
    // ]);

    // DB::table('role')->insert([
    //     'id' => 7,
    //     'name' => "CS Poliklinik Mata",
    //     'loket_id' => 5
    // ]);

    // DB::table('role')->insert([
    //     'id' => 8,
    //     'name' => "CS Poliklinik Anak",
    //     'loket_id' => 6
    // ]);

    // DB::table('role')->insert([
    //     'id' => 9,
    //     'name' => "CS Poliklinik Urologi",
    //     'loket_id' => 7
    // ]);

    // DB::table('role')->insert([
    //     'id' => 10,
    //     'name' => "CS Poliklinik Kulit",
    //     'loket_id' => 8
    // ]);
  }
}
