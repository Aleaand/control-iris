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
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinancialIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Inicializar precios base en PriceLog con campos correctos
        PriceLog::create(['item_type' => 'training', 'new_price' => 500, 'item_id' => 0, 'old_price' => 0]);
        PriceLog::create(['item_type' => 'passport_management', 'new_price' => 200, 'item_id' => 0, 'old_price' => 0]);
        PriceLog::create(['item_type' => 'vip_transfer', 'new_price' => 100, 'item_id' => 0, 'old_price' => 0]);
    }

    /**
     * Test que verifica el descuento automático del 10% por certificado de entrenamiento.
     */
    #[Test]
    public function aplicacion_de_descuento_por_entrenamiento_iris()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create();
        
        // 1. Pasajero con certificado válido (hace 1 año)
        $passengerValid = Passenger::create([
            'user_id' => $client->id,
            'name' => 'Entrenado',
            'document_number' => 'CERT-001',
            'document_country' => 'ESP',
            'birth_date' => '1990-01-01',
            'physical_fitness' => 'Apto',
            'training_certificate_status' => 'Apto',
            'training_certificate_date' => now()->subYear()
        ]);

        // 2. Vuelo de 10.000€
        $ship = Starship::create(['name' => 'SHIP', 'general_capacity' => 10, 'vip_capacity' => 10, 'operational_cost_per_au' => 0, 'cruise_speed_au' => 1, 'crew_hourly_rate' => 0, 'crew_daily_rate' => 0]);
        $dest = Destination::create(['name' => 'T', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'T']);
        $flight = Flight::create([
            'flight_code' => 'DISC-001',
            'starship_id' => $ship->id,
            'destination_id' => $dest->id,
            'origin_id' => $dest->id,
            'departure_date' => now()->addDays(10),
            'arrival_date' => now()->addDays(11),
            'base_price' => 10000,
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

        // 3. Verificar descuento en Livewire (10.000 -> 9.000)
        Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('user_id', $client->id)
            ->set('passenger_id', $passengerValid->id)
            ->set('space_flight_id', $flight->id)
            ->set('seat_type', 'nova')
            ->assertSet('discount_applied', true)
            ->assertSet('total_price', 9000);

        // 4. Pasajero con certificado expirado (hace 4 años)
        $passengerExpired = Passenger::create([
            'user_id' => $client->id,
            'name' => 'Expirado',
            'document_number' => 'CERT-002',
            'document_country' => 'ESP',
            'birth_date' => '1990-01-01',
            'physical_fitness' => 'Apto',
            'training_certificate_status' => 'Apto',
            'training_certificate_date' => now()->subYears(4)
        ]);

        Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('user_id', $client->id)
            ->set('passenger_id', $passengerExpired->id)
            ->set('space_flight_id', $flight->id)
            ->set('seat_type', 'nova')
            ->assertSet('discount_applied', false)
            ->assertSet('total_price', 10000);
    }

    /**
     * Test que verifica el cálculo correcto de adendas (solo cobrar la diferencia).
     */
    #[Test]
    public function calculo_de_adenda_solo_diferencia_de_precio()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create();
        $passenger = Passenger::create([
            'user_id' => $client->id,
            'name' => 'Upgrade User',
            'document_number' => 'UPG-001',
            'document_country' => 'ESP',
            'birth_date' => '1990-01-01',
            'physical_fitness' => 'Apto'
        ]);

        $ship = Starship::create(['name' => 'SHIP', 'general_capacity' => 10, 'vip_capacity' => 10, 'operational_cost_per_au' => 0, 'cruise_speed_au' => 1, 'crew_hourly_rate' => 0, 'crew_daily_rate' => 0]);
        $dest = Destination::create(['name' => 'T', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'T']);
        $flight = Flight::create([
            'flight_code' => 'UPG-001',
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
        ]);

        // 1. Crear Reserva Original de 1.000€
        $parent = Reservation::create([
            'user_id' => $client->id,
            'passenger_id' => $passenger->id,
            'space_flight_id' => $flight->id,
            'seat_type' => 'nova',
            'total_price' => 1000,
            'status' => 'Confirmada',
            'payment_status' => 'paid'
        ]);

        // 2. Simular Upgrade a Supernova (Multiplicador 2.5x -> 2.500€)
        // La Adenda debería costar 2.500 - 1.000 = 1.500€
        Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('isAdendaMode', true)
            ->set('adendaParentId', $parent->id)
            ->set('user_id', $client->id)
            ->set('passenger_id', $passenger->id)
            ->set('space_flight_id', $flight->id)
            ->set('seat_type', 'supernova')
            ->assertSet('paid_amount', 1000)
            ->assertSet('total_price', 1500); // 2500 total - 1000 pagados
    }

    /**
     * Test que verifica ajustes manuales (Porcentaje y Fijo).
     */
    #[Test]
    public function ajustes_manuales_aplicados_correctamente()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create();
        $passenger = Passenger::create(['user_id' => $client->id, 'name' => 'P', 'document_number' => 'P', 'document_country' => 'ESP', 'birth_date' => '1990-01-01', 'physical_fitness' => 'Apto']);
        $ship = Starship::create(['name' => 'SHIP', 'general_capacity' => 10, 'vip_capacity' => 10, 'operational_cost_per_au' => 0, 'cruise_speed_au' => 1, 'crew_hourly_rate' => 0, 'crew_daily_rate' => 0]);
        $dest = Destination::create(['name' => 'T', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'T']);
        $flight = Flight::create(['flight_code' => 'ADJ-001', 'starship_id' => $ship->id, 'destination_id' => $dest->id, 'origin_id' => $dest->id, 'departure_date' => now()->addDays(10), 'arrival_date' => now()->addDays(11), 'base_price' => 1000, 'au_distance' => 1, 'operational_cost' => 0, 'mission_speed_au' => 1, 'crew_hourly_rate' => 0, 'crew_daily_rate' => 0, 'mission_profitability' => 0, 'launch_cost_earth' => 0, 'launch_cost_planet' => 0, 'landing_cost_earth' => 0, 'landing_cost_planet' => 0]);

        // Ajuste Porcentual (10% de 1.000 = 100 desc -> 900)
        Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('user_id', $client->id)
            ->set('passenger_id', $passenger->id)
            ->set('space_flight_id', $flight->id)
            ->set('seat_type', 'nova')
            ->set('manual_adjustment_type', 'pct')
            ->set('manual_adjustment_value', 10)
            ->assertSet('total_price', 900);

        // Ajuste Fijo (1.000 -> 850)
        Livewire::actingAs($admin)
            ->test(ManageReservations::class)
            ->set('user_id', $client->id)
            ->set('passenger_id', $passenger->id)
            ->set('space_flight_id', $flight->id)
            ->set('seat_type', 'nova')
            ->set('manual_adjustment_type', 'fixed')
            ->set('manual_adjustment_value', 150)
            ->assertSet('total_price', 850);
    }
}
