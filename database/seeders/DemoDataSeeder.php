<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Location;
use App\Models\Hotel;
use App\Models\TerrestrialFlight;
use App\Models\Flight;
use App\Models\Destination;
use App\Models\Starship;
use App\Models\Passport;
use App\Models\MedicalCertificate;
use App\Models\Reservation;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. LOCATIONS
        $locations = [
            ['code' => 'MAD', 'name' => 'Madrid'],
            ['code' => 'JFK', 'name' => 'New York'],
            ['code' => 'NRT', 'name' => 'Tokyo'],
            ['code' => 'DXB', 'name' => 'Dubai'],
            ['code' => 'LHR', 'name' => 'London']
        ];
        foreach ($locations as $loc) {
            Location::firstOrCreate(['code' => $loc['code']], $loc);
        }

        $mad = Location::where('code', 'MAD')->first();
        $jfk = Location::where('code', 'JFK')->first();
        $nrt = Location::where('code', 'NRT')->first();

        // 2. HOTELS (PRE-LAUNCH MANORS)
        $hotel1 = Hotel::firstOrCreate(['name' => 'Iris Nebula Manor Madrid'], [
            'location_id' => $mad->id,
            'galactic_stars' => 5,
            'price_per_night' => 1200.00,
            'total_rooms' => 50,
        ]);
        $hotel2 = Hotel::firstOrCreate(['name' => 'Iris Nova Suites NYC'], [
            'location_id' => $jfk->id,
            'galactic_stars' => 5,
            'price_per_night' => 2500.00,
            'total_rooms' => 30,
        ]);

        // 3. TERRESTRIAL FLIGHTS (VIP JETS)
        $tflight = TerrestrialFlight::firstOrCreate(['airline' => 'Iris Black Jet'], [
            'origin_id' => $jfk->id,
            'destination_id' => $mad->id,
            'departure_datetime' => Carbon::now()->addDays(5),
            'arrival_datetime' => Carbon::now()->addDays(5)->addHours(8),
            'price' => 8500.00,
            'executive_capacity' => 12,
        ]);

        // 4. SPACE FLIGHTS
        $mars = Destination::firstOrCreate(['name' => 'Marte'], ['distance_au' => 1.52, 'description' => 'Colonia Beta']);
        $starship = Starship::firstOrCreate(['name' => 'Iris One'], ['general_capacity' => 100, 'vip_capacity' => 10]);
        
        $spaceFlight = Flight::firstOrCreate(['flight_code' => 'M-X01'], [
            'destination_id' => $mars->id,
            'starship_id' => $starship->id,
            'departure_date' => Carbon::now()->addDays(15),
            'arrival_date' => Carbon::now()->addDays(90),
            'base_price' => 250000.00,
        ]);

        // 5. GESTORES
        $gestor1 = User::firstOrCreate(['email' => 'gestor.alfa@iris.com'], [
            'name' => 'Helena',
            'primarylastname' => 'Troy',
            'role' => 'gestor',
            'password' => Hash::make('IrisGestor*1'),
            'birth_date' => '1985-05-15',
            'phone' => '+34600100200',
        ]);

        $gestor2 = User::firstOrCreate(['email' => 'gestor.beta@iris.com'], [
            'name' => 'Marcus',
            'primarylastname' => 'Aurelius',
            'role' => 'gestor',
            'password' => Hash::make('IrisGestor*2'),
            'birth_date' => '1990-10-20',
            'phone' => '+15550001234',
        ]);

        // 6. CLIENTES
        $cliente1 = User::firstOrCreate(['email' => 'hugo.v@millonarios.com'], [
            'name' => 'Hugo',
            'primarylastname' => 'Vanderbilt',
            'role' => 'cliente',
            'password' => Hash::make('Cliente123'),
            'birth_date' => '1970-02-14',
            'phone' => '+15559876543',
            'assigned_manager_id' => $gestor1->id,
        ]);

        $cliente2 = User::firstOrCreate(['email' => 'victoria.x@luxury.com'], [
            'name' => 'Victoria',
            'primarylastname' => 'Cross',
            'role' => 'cliente',
            'password' => Hash::make('Cliente123'),
            'birth_date' => '1995-08-30',
            'phone' => '+447000888999',
            'assigned_manager_id' => $gestor1->id,
        ]);

        // 7. BIOMETRICS & PASSPORTS
        Passport::firstOrCreate(['user_id' => $cliente1->id], [
            'number' => 'IP-88X-900',
            'expiration_date' => Carbon::now()->addYears(8),
            'is_valid' => true,
        ]);

        MedicalCertificate::firstOrCreate(['user_id' => $cliente1->id], [
            'issue_date' => Carbon::now()->subMonths(6),
            'status' => 'Apto',
        ]);

        // Cliente 2 has expired or no biometrics yet (to show error states)
        MedicalCertificate::firstOrCreate(['user_id' => $cliente2->id], [
            'issue_date' => Carbon::now()->subMonths(1),
            'status' => 'No Apto',
        ]);

        // 8. RESERVATIONS
        $res1 = Reservation::create([
            'user_id' => $cliente1->id,
            'space_flight_id' => $spaceFlight->id,
            'seat_type' => 'Ejecutiva',
            'seat_number' => '1A',
            'total_price' => 750000.00,
            'discount_applied' => false,
            'status' => 'Confirmada',
        ]);

        $res1->logistics()->create([
            'terrestrial_flight_id' => $tflight->id,
            'hotel_id' => $hotel1->id,
            'hotel_nights' => 3,
            'training_included' => true,
            'vip_transfer_included' => true,
        ]);

        $res2 = Reservation::create([
            'user_id' => $cliente2->id,
            'space_flight_id' => $spaceFlight->id,
            'seat_type' => 'Turista',
            'seat_number' => '14B',
            'total_price' => 250000.00,
            'discount_applied' => true,
            'status' => 'Pendiente',
        ]);

        $res2->logistics()->create([
            'terrestrial_flight_id' => null,
            'hotel_id' => $hotel2->id,
            'hotel_nights' => 1,
            'training_included' => true,
            'vip_transfer_included' => false,
        ]);
    }
}
