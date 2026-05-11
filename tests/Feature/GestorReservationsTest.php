<?php

namespace Tests\Feature;

use App\Models\Passenger;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Flight;
use App\Models\Starship;
use App\Models\Destination;
use App\Livewire\Gestor\GestorReservations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestorReservationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $gestor;
    protected User $client;
    protected Passenger $passenger;
    protected Flight $flight;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->client = User::factory()->create(['role' => 'cliente', 'assigned_manager_id' => $this->gestor->id]);
        $this->passenger = Passenger::create([
            'user_id' => $this->client->id,
            'name' => 'John',
            'primarylastname' => 'Doe',
            'document_number' => '12345678X',
            'document_country' => 'ESP',
            'birth_date' => '1990-01-01',
            'physical_fitness' => 'Apto'
        ]);

        $ship = Starship::create(['name' => 'Ship 1', 'general_capacity' => 10, 'vip_capacity' => 5, 'operational_cost_per_au' => 100, 'cruise_speed_au' => 0.5, 'crew_hourly_rate' => 50, 'crew_daily_rate' => 500]);
        $origin = Destination::create(['name' => 'Earth', 'distance_au' => 0, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'Home']);
        $dest = Destination::create(['name' => 'Mars', 'distance_au' => 0.5, 'launch_fee' => 1000, 'landing_fee' => 1000, 'description' => 'Red Planet']);

        $this->flight = Flight::create([
            'flight_code' => 'FL-001',
            'starship_id' => $ship->id,
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'departure_date' => now()->addDays(30),
            'arrival_date' => now()->addDays(31),
            'base_price' => 5000,
            'au_distance' => 0.5,
            'operational_cost' => 1000,
            'mission_speed_au' => 0.5,
            'crew_hourly_rate' => 100,
            'crew_daily_rate' => 1000,
            'launch_cost_earth' => 500,
            'launch_cost_planet' => 500,
            'landing_cost_earth' => 500,
            'landing_cost_planet' => 500,
            'mission_profitability' => 0,
            'status' => 'scheduled'
        ]);
    }

    #[Test]
    public function el_gestor_solo_puede_ver_las_reservas_de_sus_clientes_asignados()
    {
        $otherGestor = User::factory()->create(['role' => 'gestor']);
        $otherClient = User::factory()->create(['role' => 'cliente', 'assigned_manager_id' => $otherGestor->id]);
        
        $myRes = Reservation::create([
            'user_id' => $this->client->id,
            'passenger_id' => $this->passenger->id,
            'space_flight_id' => $this->flight->id,
            'status' => 'Confirmada',
            'total_price' => 5000,
            'id_locator' => 'MY-LOC'
        ]);

        $otherRes = Reservation::create([
            'user_id' => $otherClient->id,
            'passenger_id' => $this->passenger->id,
            'space_flight_id' => $this->flight->id,
            'status' => 'Confirmada',
            'total_price' => 5000,
            'id_locator' => 'OTHER-LOC'
        ]);

        Livewire::actingAs($this->gestor)
            ->test(GestorReservations::class)
            ->assertSee('MY-LOC')
            ->assertDontSee('OTHER-LOC');
    }

    #[Test]
    public function el_gestor_puede_buscar_reservas_por_localizador_sin_distinguir_mayusculas()
    {
        Reservation::create([
            'user_id' => $this->client->id,
            'passenger_id' => $this->passenger->id,
            'space_flight_id' => $this->flight->id,
            'status' => 'Confirmada',
            'total_price' => 5000,
            'id_locator' => 'IRIS-999'
        ]);

        Livewire::actingAs($this->gestor)
            ->test(GestorReservations::class)
            ->set('search', 'iris-999')
            ->assertSee('IRIS-999');
    }

    #[Test]
    public function el_gestor_puede_eliminar_una_reserva()
    {
        $res = Reservation::create([
            'user_id' => $this->client->id,
            'passenger_id' => $this->passenger->id,
            'space_flight_id' => $this->flight->id,
            'status' => 'Confirmada',
            'total_price' => 5000,
            'id_locator' => 'TO-DELETE'
        ]);

        Livewire::actingAs($this->gestor)
            ->test(GestorReservations::class)
            ->call('confirmDelete', $res->id)
            ->call('deleteReservation')
            ->assertSee('eliminada');

        $this->assertNull(Reservation::find($res->id));
    }
}
