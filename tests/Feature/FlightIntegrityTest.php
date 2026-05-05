<?php

namespace Tests\Feature;

use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Reservation;
use App\Models\Starship;
use App\Models\Destination;
use App\Models\User;
use App\Livewire\Admin\ManageReservations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FlightIntegrityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que verifica la protección contra Overbooking.
     */
    public function test_cannot_overbook_flight_capacity()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // 1. Crear Nave con capacidad mínima (1 VIP)
        $ship = Starship::create([
            'name' => 'TINY-SHIP',
            'general_capacity' => 0,
            'vip_capacity' => 1,
            'operational_cost_per_au' => 0,
            'cruise_speed_au' => 1,
            'crew_hourly_rate' => 0,
            'crew_daily_rate' => 0,
            'status' => 'active'
        ]);

        $dest = Destination::create([
            'name' => 'Test', 
            'distance_au' => 1, 
            'launch_fee' => 0, 
            'landing_fee' => 0, 
            'description' => 'Test'
        ]);

        // 2. Crear Vuelo
        $flight = Flight::create([
            'flight_code' => 'TEST-001',
            'starship_id' => $ship->id,
            'destination_id' => $dest->id,
            'origin_id' => $dest->id,
            'departure_date' => now()->addDays(10),
            'arrival_date' => now()->addDays(11),
            'base_price' => 1000,
            'au_distance' => 1,
            'operational_cost' => 0,
            'mission_speed_au' => 1,
            'crew_hourly_rate' => 0,
            'crew_daily_rate' => 0,
            'mission_profitability' => 0,
            'launch_cost_earth' => 0,
            'launch_cost_planet' => 0,
            'landing_cost_earth' => 0,
            'landing_cost_planet' => 0,
            'status' => 'scheduled'
        ]);

        // 3. Crear una reserva previa para llenar el cupo
        $client = User::factory()->create();
        $passenger1 = Passenger::create([
            'user_id' => $client->id,
            'name' => 'P1',
            'document_number' => 'D1',
            'document_country' => 'ESP',
            'birth_date' => '1990-01-01',
            'physical_fitness' => 'Apto'
        ]);

        Reservation::create([
            'user_id' => $client->id,
            'passenger_id' => $passenger1->id,
            'space_flight_id' => $flight->id,
            'seat_type' => 'supernova',
            'status' => 'Confirmada',
            'payment_status' => 'paid'
        ]);

        // 4. Intentar reservar otro pasajero en la misma clase (VIP) vía Livewire
        $passenger2 = Passenger::create([
            'user_id' => $client->id,
            'name' => 'P2',
            'document_number' => 'D2',
            'document_country' => 'ESP',
            'birth_date' => '1990-01-01',
            'physical_fitness' => 'Apto'
        ]);

        Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('groupMode', true)
            ->set('user_id', $client->id)
            ->set('space_flight_id', $flight->id)
            ->set('selectedPassengers', [
                [
                    'passenger_id' => $passenger2->id,
                    'name' => 'P2',
                    'seat_type' => 'supernova',
                    'total_price' => 1000,
                    'training_included' => false,
                    'passport_management_included' => false,
                    'refund_insurance_included' => false,
                    'vip_transfer_included' => false,
                    'hotel_id' => null,
                    'terrestrial_flight_id' => null,
                ]
            ])
            ->call('executeSave')
            ->assertDispatched('swal:alert', function($name, $data) {
                return $data[0]['title'] === 'Error de Cupo';
            });
    }

    /**
     * Test que verifica la prevención de conflictos de fecha para un mismo pasajero.
     */
    public function test_passenger_cannot_have_overlapping_flights()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ship = Starship::create(['name' => 'SHIP', 'general_capacity' => 10, 'vip_capacity' => 10, 'operational_cost_per_au' => 0, 'cruise_speed_au' => 1, 'crew_hourly_rate' => 0, 'crew_daily_rate' => 0]);
        $dest = Destination::create([
            'name' => 'Test', 
            'distance_au' => 1, 
            'launch_fee' => 0, 
            'landing_fee' => 0, 
            'description' => 'Test'
        ]);
        
        $date = now()->addDays(20);

        // Vuelo A y Vuelo B el mismo día
        $flightA = Flight::create([
            'flight_code' => 'FLIGHT-A',
            'starship_id' => $ship->id,
            'destination_id' => $dest->id,
            'origin_id' => $dest->id,
            'departure_date' => $date,
            'arrival_date' => $date->copy()->addDays(1),
            'base_price' => 1000,
            'au_distance' => 1,
            'operational_cost' => 0,
            'mission_speed_au' => 1,
            'crew_hourly_rate' => 0,
            'crew_daily_rate' => 0,
            'mission_profitability' => 0,
            'launch_cost_earth' => 0,
            'launch_cost_planet' => 0,
            'landing_cost_earth' => 0,
            'landing_cost_planet' => 0,
        ]);

        $flightB = Flight::create([
            'flight_code' => 'FLIGHT-B',
            'starship_id' => $ship->id,
            'destination_id' => $dest->id,
            'origin_id' => $dest->id,
            'departure_date' => $date, // MISMO DÍA
            'arrival_date' => $date->copy()->addDays(1),
            'base_price' => 1000,
            'au_distance' => 1,
            'operational_cost' => 0,
            'mission_speed_au' => 1,
            'crew_hourly_rate' => 0,
            'crew_daily_rate' => 0,
            'mission_profitability' => 0,
            'launch_cost_earth' => 0,
            'launch_cost_planet' => 0,
            'landing_cost_earth' => 0,
            'landing_cost_planet' => 0,
        ]);

        $client = User::factory()->create();
        $passenger = Passenger::create([
            'user_id' => $client->id,
            'name' => 'Viajero',
            'document_number' => 'DOC-001',
            'document_country' => 'ESP',
            'birth_date' => '1990-01-01',
            'physical_fitness' => 'Apto'
        ]);

        // 1. Reservar en Vuelo A
        Reservation::create([
            'user_id' => $client->id,
            'passenger_id' => $passenger->id,
            'space_flight_id' => $flightA->id,
            'status' => 'Confirmada',
        ]);

        // 2. Intentar reservar en Vuelo B vía Livewire
        Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('groupMode', true)
            ->set('user_id', $client->id)
            ->set('space_flight_id', $flightB->id)
            ->set('selectedPassengers', [
                [
                    'passenger_id' => $passenger->id,
                    'name' => 'Viajero',
                    'seat_type' => 'nova',
                    'total_price' => 1000,
                    'training_included' => false,
                    'passport_management_included' => false,
                    'refund_insurance_included' => false,
                    'vip_transfer_included' => false,
                    'hotel_id' => null,
                    'terrestrial_flight_id' => null,
                ]
            ])
            ->call('executeSave')
            ->assertDispatched('swal:alert', function($name, $data) {
                return $data[0]['title'] === 'Conflicto Logístico';
            });
    }
}
