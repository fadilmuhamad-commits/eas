<?php

namespace Database\Seeders;

use App\Models\M_Permission;
use App\Models\M_Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoRole extends Seeder
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
  }
}
