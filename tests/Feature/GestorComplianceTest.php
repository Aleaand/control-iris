<?php

namespace Tests\Feature;

use App\Models\Passenger;
use App\Models\Task;
use App\Models\User;
use App\Livewire\Gestor\GestorCompliance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestorComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected User $gestor;
    protected User $client;
    protected Passenger $passenger;

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
            'physical_fitness' => 'No apto',
            'training_certificate_status' => 'No Apto'
        ]);
    }

    #[Test]
    public function se_aplica_la_regla_de_3_horas_para_nuevo_entrenamiento()
    {
        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('openTrainingForPassenger', $this->passenger->id)
            ->set('trainingCertStatus', 'Apto')
            ->call('saveTrainingCert')
            ->assertHasNoErrors()
            ->assertSee('inferior a las 3 necesarias');

        $this->assertEquals('No Apto', $this->passenger->fresh()->training_certificate_status);
    }

    #[Test]
    public function se_permite_apto_si_se_completan_3_horas()
    {
        $task = Task::create([
            'assigned_gestor_id' => $this->gestor->id,
            'created_by' => $this->gestor->id,
            'type' => 'iris-training',
            'title' => 'Training John',
            'payload' => [
                'passenger_id' => $this->passenger->id,
                'sessions' => [
                    ['date' => '2026-05-01 10:00', 'hours' => 3, 'status' => 'Completada']
                ]
            ]
        ]);

        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('openTrainingForPassenger', $this->passenger->id)
            ->set('trainingCertStatus', 'Apto')
            ->call('saveTrainingCert')
            ->assertHasNoErrors()
            ->assertSee('Datos actualizados con éxito');

        $this->assertEquals('Apto', $this->passenger->fresh()->training_certificate_status);
        $this->assertEquals('Apto', $this->passenger->fresh()->physical_fitness);
    }

    #[Test]
    public function se_aplica_la_regla_de_1_hora_para_renovaciones()
    {
        // Mark as having a previous date to trigger renewal logic
        $this->passenger->update(['training_certificate_date' => '2020-01-01']);

        $task = Task::create([
            'assigned_gestor_id' => $this->gestor->id,
            'created_by' => $this->gestor->id,
            'type' => 'iris-training',
            'title' => 'Training John Renewal',
            'payload' => [
                'passenger_id' => $this->passenger->id,
                'sessions' => [
                    ['date' => '2026-05-01 10:00', 'hours' => 1, 'status' => 'Completada']
                ]
            ]
        ]);

        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('openTrainingForPassenger', $this->passenger->id)
            ->set('trainingCertStatus', 'Apto')
            ->call('saveTrainingCert')
            ->assertHasNoErrors()
            ->assertSee('Datos actualizados con éxito');

        $this->assertEquals('Apto', $this->passenger->fresh()->training_certificate_status);
    }

    #[Test]
    public function se_detectan_certificados_expirados()
    {
        // Certificado de hace 11 años (Límite es 10)
        $this->passenger->update(['training_certificate_date' => now()->subYears(11)]);

        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('openTrainingForPassenger', $this->passenger->id)
            ->assertSet('trainingCertExpired', true)
            ->assertSet('trainingCertStatus', 'No Apto');
    }

    #[Test]
    public function se_envia_correo_cuando_se_programan_sesiones()
    {
        Mail::fake();

        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('openTrainingForPassenger', $this->passenger->id)
            ->set('newSessions', [
                ['date' => '2026-06-01 10:00', 'hours' => 3]
            ])
            ->call('addTrainingSession');

        Mail::assertSent(\App\Mail\TrainingScheduledMail::class, function ($mail) {
            return $mail->hasTo($this->client->email) && count($mail->sessions) === 1;
        });
    }

    #[Test]
    public function se_envia_correo_al_cancelar_una_sesion()
    {
        Mail::fake();

        $task = Task::create([
            'assigned_gestor_id' => $this->gestor->id,
            'created_by' => $this->gestor->id,
            'type' => 'iris-training',
            'title' => 'Training John Cancel',
            'payload' => [
                'passenger_id' => $this->passenger->id,
                'sessions' => [
                    ['date' => '2026-06-01 10:00', 'hours' => 1, 'status' => 'Programada']
                ]
            ]
        ]);

        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('openTrainingModal', $task->id)
            ->call('updateTrainingSessionStatus', 0, 'Ausente');

        Mail::assertSent(\App\Mail\TrainingSessionStatusMail::class, function ($mail) {
            return $mail->newStatus === 'Ausente';
        });
    }
}
