<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Super',
            'primarylastname' => 'Admin',
            'email' => 'admin@iris.com',
            'password' => bcrypt('SuperAdmin*'),
            'role' => 'super_admin',
            'birth_date' => '2000-01-01',
        ]);
    }
}
