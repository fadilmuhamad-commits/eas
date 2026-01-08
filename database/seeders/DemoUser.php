<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => "Alpukat Buah",
            'username' => "admin",
            'email' => "alpuket@gmail.com",
            'password' => Hash::make('0'),
            'role_id' => 1
        ]);
    }
}
