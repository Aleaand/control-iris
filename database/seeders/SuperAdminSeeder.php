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
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@iris.com'],
            [
                'name' => 'Super',
                'primarylastname' => 'Admin',
                'secondarylastname' => 'Iris',
                'password' => \Illuminate\Support\Facades\Hash::make('SuperAdmin*'),
                'role' => 'super_admin',
                'birth_date' => '2000-01-01',
                'phone' => '000000000',
                'email_verified_at' => now(),
            ]
        );
    }
}
