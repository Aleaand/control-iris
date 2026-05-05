<?php

namespace Tests\Feature;

use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Reservation;
use App\Models\Starship;
use App\Models\Destination;
use App\Models\User;
use App\Models\PriceLog;
use App\Livewire\Admin\ManageReservations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        PriceLog::create(['item_type' => 'training', 'new_price' => 500, 'item_id' => 0, 'old_price' => 0]);
        PriceLog::create(['item_type' => 'passport_management', 'new_price' => 200, 'item_id' => 0, 'old_price' => 0]);
        PriceLog::create(['item_type' => 'vip_transfer', 'new_price' => 100, 'item_id' => 0, 'old_price' => 0]);
    }

    public function test_can_create_group_reservation_for_multiple_passengers()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'cliente']);
        
        $ship = Starship::create(['name' => 'G-SHIP', 'general_capacity' => 10, 'vip_capacity' => 10, 'operational_cost_per_au' => 0, 'cruise_speed_au' => 1, 'crew_hourly_rate' => 0, 'crew_daily_rate' => 0]);
        $dest = Destination::create(['name' => 'T', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'T']);
        $flight = Flight::create([
            'flight_code' => 'GRP-001',
            'starship_id' => $ship->id,
            'destination_id' => $dest->id,
            'origin_id' => $dest->id,
            'departure_date' => now()->addDays(30),
            'arrival_date' => now()->addDays(31),
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

        $passenger1 = Passenger::create(['user_id' => $client->id, 'name' => 'Pass 1', 'document_number' => 'P1', 'document_country' => 'ESP', 'birth_date' => '1990-01-01', 'physical_fitness' => 'Apto']);
        $passenger2 = Passenger::create(['user_id' => $client->id, 'name' => 'Pass 2', 'document_number' => 'P2', 'document_country' => 'ESP', 'birth_date' => '1990-01-01', 'physical_fitness' => 'Apto']);

        $testable = Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('groupMode', true)
            ->set('user_id', $client->id)
            ->set('space_flight_id', $flight->id)
            ->set('selectedPassengers', [
                [
                    'passenger_id' => $passenger1->id,
                    'name' => 'Pass 1',
                    'seat_type' => 'nova',
                    'total_price' => 1000,
                    'training_included' => true,
                    'passport_management_included' => false,
                    'refund_insurance_included' => false,
                    'vip_transfer_included' => false,
                    'hotel_id' => null,
                    'terrestrial_flight_id' => null,
                ],
                [
                    'passenger_id' => $passenger2->id,
                    'name' => 'Pass 2',
                    'seat_type' => 'nova',
                    'total_price' => 1200,
                    'training_included' => false,
                    'passport_management_included' => true,
                    'refund_insurance_included' => false,
                    'vip_transfer_included' => false,
                    'hotel_id' => null,
                    'terrestrial_flight_id' => null,
                ]
            ])
            ->call('executeSave');

        $testable->assertDispatched('swal:alert');
        $this->assertEquals(2, Reservation::count());
    }

    public function test_can_create_reservation_without_flight_for_only_training()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'cliente']);
        $passenger = Passenger::create(['user_id' => $client->id, 'name' => 'Solo Tramite', 'document_number' => 'T1', 'document_country' => 'ESP', 'birth_date' => '1990-01-01', 'physical_fitness' => 'Apto']);

        $testable = Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('groupMode', false)
            ->set('user_id', $client->id)
            ->set('passenger_id', $passenger->id)
            ->set('space_flight_id', null)
            ->set('training_included', true)
            ->set('passport_management_included', true)
            ->call('executeSave');
            
        $testable->assertDispatched('swal:alert');
        $this->assertEquals(1, Reservation::count());
    }
}
