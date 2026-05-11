<?php

namespace Tests\Feature;

use App\Models\Passenger;
use App\Models\User;
use App\Models\Task;
use App\Livewire\Gestor\GestorCompliance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestorPassportTest extends TestCase
{
    use RefreshDatabase;

    protected User $gestor;
    protected User $client;
    protected Passenger $passenger;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Mail::fake();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->client = User::factory()->create(['role' => 'cliente', 'assigned_manager_id' => $this->gestor->id]);
        $this->passenger = Passenger::create([
            'user_id' => $this->client->id,
            'name' => 'Jane',
            'primarylastname' => 'Smith',
            'document_number' => '87654321Y',
            'document_country' => 'ESP',
            'birth_date' => '1995-05-05',
            'physical_fitness' => 'Apto'
        ]);
    }

    #[Test]
    public function identifica_pasajeros_que_necesitan_pasaporte()
    {
        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('identifyPassportNeeds')
            ->assertSee('Jane Smith');
    }

    #[Test]
    public function puede_finalizar_el_tramite_de_pasaporte_con_subida_de_pdf()
    {
        $file = UploadedFile::fake()->create('passport.pdf', 500);

        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->set('finalizarPax', $this->passenger->toArray())
            ->set('final_passport_number', 'IRIS-PASS-123')
            ->set('final_passport_expiration', now()->addYears(5)->format('Y-m-d'))
            ->set('final_passport_pdf', $file)
            ->call('finalizarTramitePasaporte')
            ->assertHasNoErrors();

        $this->passenger->refresh();
        $this->assertEquals('IRIS-PASS-123', $this->passenger->iris_passport_number);
        $this->assertEquals('active', $this->passenger->passport_status);
        $this->assertNotNull($this->passenger->passport_pdf);
        
        Storage::disk('public')->assertExists($this->passenger->passport_pdf);
        Mail::assertSent(\App\Mail\PassportApprovedMail::class);
    }

    #[Test]
    public function puede_ejecutar_el_chequeo_masivo_ofac_y_mostrar_el_resumen()
    {
        Livewire::actingAs($this->gestor)
            ->test(GestorCompliance::class)
            ->call('runBulkOfacCheck')
            ->assertSet('showBulkOfacModal', true)
            ->assertSee('Total');
    }
}
