<?php

namespace Tests\Feature;

use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Reservation;
use App\Models\Starship;
use App\Models\Destination;
use App\Models\User;
use App\Models\Task;
use App\Livewire\Admin\ManageFlights;
use App\Livewire\Admin\ManageReservations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Admin cancela un vuelo y se generan tareas para los gestores.
     */
    public function test_tasks_are_generated_for_gestors_when_flight_is_cancelled()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $gestor = User::factory()->create(['role' => 'gestor']);
        $client = User::factory()->create(['role' => 'cliente', 'name' => 'Client One', 'assigned_manager_id' => $gestor->id]);
        
        $ship = Starship::create([
            'name' => 'T-SHIP', 
            'general_capacity' => 10, 
            'vip_capacity' => 10, 
            'operational_cost_per_au' => 0, 
            'cruise_speed_au' => 1, 
            'crew_hourly_rate' => 0, 
            'crew_daily_rate' => 0,
            'status' => 'active'
        ]);
        $dest1 = Destination::create(['name' => 'Earth', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'E']);
        $dest2 = Destination::create(['name' => 'Mars', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'M']);
        
        $flight = Flight::create([
            'flight_code' => 'CNL-001',
            'starship_id' => $ship->id,
            'destination_id' => $dest2->id,
            'origin_id' => $dest1->id,
            'departure_date' => now()->addDays(10),
            'arrival_date' => now()->addDays(11),
            'base_price' => 1000,
            'status' => 'scheduled',
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

        $passenger = Passenger::create([
            'user_id' => $client->id, 
            'name' => 'Affected', 
            'primarylastname' => 'Pass', 
            'document_number' => 'A1', 
            'document_country' => 'ESP', 
            'birth_date' => '1990-01-01', 
            'physical_fitness' => 'Apto'
        ]);

        // Crear reserva para el pasajero del gestor
        $res = Reservation::create([
            'user_id' => $client->id,
            'passenger_id' => $passenger->id,
            'space_flight_id' => $flight->id,
            'status' => 'Confirmada',
            'payment_status' => 'paid',
            'total_price' => 1000,
            'id_locator' => 'LOC-123'
        ]);

        // Simular cancelación desde ManageFlights
        Livewire::actingAs($admin)
            ->test(ManageFlights::class)
            ->set('deleteId', $flight->id)
            ->set('cancelReason', 'technical')
            ->call('cancelFlightAndNotify');

        // Verificar que el vuelo y reserva están cancelados
        $this->assertEquals('cancelled', $flight->fresh()->status);
        $this->assertEquals('Cancelada', $res->fresh()->status);

        // Verificar que se creó la tarea para el gestor
        $this->assertDatabaseHas('tasks', [
            'assigned_gestor_id' => $gestor->id,
            'type' => 'flight_cancelled',
            'priority' => 'urgente'
        ]);

        $task = Task::where('assigned_gestor_id', $gestor->id)->first();
        $this->assertStringContainsString('Affected Pass', $task->description);
        $this->assertStringContainsString('causa técnica', $task->description);
    }

    /**
     * Test: Admin retrasa un vuelo y se generan tareas de notificación.
     */
    public function test_tasks_are_generated_when_flight_is_delayed()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $gestor = User::factory()->create(['role' => 'gestor']);
        $client = User::factory()->create(['role' => 'cliente', 'name' => 'Client Two', 'assigned_manager_id' => $gestor->id]);
        
        $ship = Starship::create([
            'name' => 'D-SHIP', 
            'general_capacity' => 10, 
            'vip_capacity' => 10, 
            'operational_cost_per_au' => 0, 
            'cruise_speed_au' => 1, 
            'crew_hourly_rate' => 0, 
            'crew_daily_rate' => 0,
            'status' => 'active'
        ]);
        $dest1 = Destination::create(['name' => 'Earth', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'E']);
        $dest2 = Destination::create(['name' => 'Mars', 'distance_au' => 1, 'launch_fee' => 0, 'landing_fee' => 0, 'description' => 'M']);
        
        $flight = Flight::create([
            'flight_code' => 'DLY-001',
            'starship_id' => $ship->id,
            'destination_id' => $dest2->id,
            'origin_id' => $dest1->id,
            'departure_date' => now()->addDays(5)->setSeconds(0),
            'arrival_date' => now()->addDays(6)->setSeconds(0),
            'base_price' => 1000,
            'status' => 'scheduled',
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

        $passenger = Passenger::create([
            'user_id' => $client->id, 
            'name' => 'Delayed', 
            'primarylastname' => 'Pass', 
            'document_number' => 'D1', 
            'document_country' => 'ESP', 
            'birth_date' => '1990-01-01', 
            'physical_fitness' => 'Apto'
        ]);
        $res = Reservation::create([
            'user_id' => $client->id, 
            'passenger_id' => $passenger->id, 
            'space_flight_id' => $flight->id, 
            'status' => 'Confirmada', 
            'payment_status' => 'paid', 
            'total_price' => 1000,
            'id_locator' => 'LOC-DLY'
        ]);

        // Simular edición de fecha (retraso)
        Livewire::actingAs($admin)
            ->test(ManageFlights::class)
            ->call('edit', $flight->id)
            ->set('departure_date', $flight->departure_date->addDays(5)->format('Y-m-d\TH:i'))
            ->call('executeSave')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tasks', [
            'assigned_gestor_id' => $gestor->id,
            'type' => 'policy_change'
        ]);
        
        $task = Task::where('assigned_gestor_id', $gestor->id)->where('type', 'policy_change')->first();
        $this->assertStringContainsString('Delayed Pass', $task->description);
    }

    /**
     * Test: Tarea automática de pasaporte al confirmar pago (Stripe simulation).
     */
    public function test_passport_task_is_created_automatically_on_payment()
    {
        $gestor = User::factory()->create(['role' => 'gestor']);
        $client = User::factory()->create(['role' => 'cliente', 'name' => 'Client Three', 'assigned_manager_id' => $gestor->id]);
        $passenger = Passenger::create([
            'user_id' => $client->id, 
            'name' => 'Passport', 
            'primarylastname' => 'Pass', 
            'document_number' => 'P1', 
            'document_country' => 'ESP', 
            'birth_date' => '1990-01-01', 
            'physical_fitness' => 'Apto'
        ]);
        
        $res = Reservation::create([
            'user_id' => $client->id,
            'passenger_id' => $passenger->id,
            'space_flight_id' => null,
            'status' => 'Pendiente',
            'payment_status' => 'pending',
            'total_price' => 500,
            'id_locator' => 'PAS-999'
        ]);
        $res->logistics()->create(['passport_management_included' => true]);

        $controller = new \App\Http\Controllers\StripeController();
        $method = new \ReflectionMethod($controller, 'automateTasks');
        $method->setAccessible(true);
        $method->invoke($controller, $res);

        $this->assertDatabaseHas('tasks', [
            'assigned_gestor_id' => $gestor->id,
            'title' => 'Gestión de Pasaporte: Passport Pass'
        ]);
    }
}
