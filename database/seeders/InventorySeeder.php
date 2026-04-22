<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Destination;
use App\Models\Starship;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    //php artisan db:seed --class=InventorySeeder
    public function run(): void
    {
        Destination::create([
            'name' => 'Marte',
            'description' => 'El susurro rojo del sistema solar. Un refugio etéreo para los que buscan lo inexplorado.',
            'distance_au' => 1.52
        ]);

        Starship::create([
            'name' => 'Iris One',
            'general_capacity' => 100,
            'vip_capacity' => 10
        ]);
    }
}
