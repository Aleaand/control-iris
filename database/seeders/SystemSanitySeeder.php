<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Starship;
use App\Models\Destination;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\TerrestrialFlight;
use App\Models\Location;
use App\Models\User;
use App\Models\Passenger;
use App\Models\Reservation;
use App\Models\ReservationLogistic;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemSanitySeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar datos previos en orden de dependencia
        Reservation::whereHas('user', fn($q) => $q->where('email', 'like', '%.sanity@iris.aero'))->forceDelete();
        Passenger::whereHas('client', fn($q) => $q->where('email', 'like', '%.sanity@iris.aero'))->forceDelete();
        User::where('email', 'like', '%.sanity@iris.aero')->forceDelete();
        Flight::where('flight_code', 'IRIS-SANITY-2026')->forceDelete();
        Starship::where('name', 'IRIS-SANITY-CHECK')->forceDelete();
        Destination::where('name', 'Marte (Sanity Zone)')->forceDelete();
        Hotel::where('name', 'Resort de Prueba')->forceDelete();
        TerrestrialFlight::where('airline', 'Iris Sanity Air')->forceDelete();
        Location::where('code', 'like', '%-SANITY')->orWhere('code', 'VN-D')->orWhere('code', 'MAD-T')->orWhere('code', 'KSC-FL')->forceDelete();

        // 0. Localizaciones (Requeridas para Hoteles y Vuelos Terrestres)
        $locVenus = Location::create(['name' => 'Venus Nube Domo', 'code' => 'VN-D', 'country_code' => 'VN']);
        $locMadrid = Location::create(['name' => 'Madrid Terminal', 'code' => 'MAD-T', 'country_code' => 'ES']);
        $locKSC = Location::create(['name' => 'Kennedy Space Center', 'code' => 'KSC-FL', 'country_code' => 'US']);

        // 1. Nave
        $ship = Starship::create([
            'name' => 'IRIS-SANITY-CHECK',
            'general_capacity' => 50,
            'vip_capacity' => 12,
            'operational_cost_per_au' => 1500.00,
            'cruise_speed_au' => 0.0125,
            'crew_hourly_rate' => 500.00,
            'crew_daily_rate' => 2500.00,
            'status' => 'Operativo'
        ]);

        // 2. Destino
        $dest = Destination::create([
            'name' => 'Marte (Sanity Zone)',
            'distance_au' => 1, 
            'launch_fee' => 15000.00,
            'landing_fee' => 7500.00,
            'description' => 'Destino de prueba para verificación de sistema.'
        ]);

        // 3. Vuelo Espacial
        $flight = Flight::create([
            'starship_id' => $ship->id,
            'destination_id' => $dest->id,
            'origin_id' => $dest->id, 
            'flight_code' => 'IRIS-SANITY-2026',
            'au_distance' => 1.5,
            'operational_cost' => 350000.00,
            'mission_speed_au' => 0.0125,
            'crew_hourly_rate' => 500.00,
            'crew_daily_rate' => 2500.00,
            'mission_profitability' => 0,
            'departure_date' => now()->addMonths(6),
            'arrival_date' => now()->addMonths(6)->addDays(15),
            'base_price' => 750000.00,
            'launch_cost_earth' => 50000.00,
            'launch_cost_planet' => 25000.00,
            'landing_cost_earth' => 50000.00,
            'landing_cost_planet' => 25000.00,
            'status' => 'Programado'
        ]);

        // 4. Hotel
        $hotel = Hotel::create([
            'name' => 'Resort de Prueba',
            'location_id' => $locVenus->id,
            'price_per_night' => 2000.00,
            'galactic_stars' => 5,
            'total_rooms' => 50
        ]);

        // 5. Vuelo Terrestre
        $tFlight = TerrestrialFlight::create([
            'airline' => 'Iris Sanity Air',
            'flight_number' => 'SA-101',
            'origin_id' => $locMadrid->id,
            'destination_id' => $locKSC->id,
            'departure_datetime' => now()->addMonths(6)->subDays(3),
            'price' => 2500.00,
            'status' => 'Programado'
        ]);

        // 6. Gestor
        $gestor = User::create([
            'name' => 'Gestor Sanity',
            'email' => 'gestor.sanity@iris.aero',
            'password' => Hash::make('password'),
            'role' => 'gestor'
        ]);

        // 7. Cliente con Pasajero
        $client = User::create([
            'name' => 'Cliente Sanity',
            'email' => 'client.sanity@iris.aero',
            'password' => Hash::make('password'),
            'role' => 'client'
        ]);

        $passenger = Passenger::create([
            'user_id' => $client->id,
            'name' => 'Pasajero',
            'primarylastname' => 'Sanity',
            'secondarylastname' => 'Check',
            'document_number' => 'SN-000-XX',
            'document_country' => 'ESP',
            'birth_date' => '1995-05-05',
            'physical_fitness' => 'Apto'
        ]);

        // 8. Reserva con TODO INCLUIDO
        $res = Reservation::create([
            'user_id' => $client->id,
            'passenger_id' => $passenger->id,
            'space_flight_id' => $flight->id,
            'id_locator' => (string) Str::uuid(),
            'seat_type' => 'supernova',
            'total_price' => 850000.00,
            'status' => 'Pendiente', 
            'payment_status' => 'pending'
        ]);

        ReservationLogistic::create([
            'reservation_id' => $res->id,
            'terrestrial_flight_id' => $tFlight->id,
            'hotel_id' => $hotel->id,
            'hotel_nights' => 7,
            'training_included' => true,
            'vip_transfer_included' => true,
            'refund_insurance_included' => true,
            'passport_management_included' => true
        ]);
    }
}
