<?php

namespace Tests\Feature;

use App\Models\Passenger;
use App\Models\Reservation;
use App\Models\User;
use App\Livewire\Gestor\GestorPayments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestorPaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $gestor;
    protected User $client;
    protected Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gestor = User::factory()->create(['role' => 'gestor']);
        $this->client = User::factory()->create(['role' => 'cliente', 'assigned_manager_id' => $this->gestor->id]);
        
        $pax = Passenger::create([
            'user_id' => $this->client->id,
            'name' => 'Alice',
            'primarylastname' => 'Wonder',
            'document_number' => '11122233K',
            'document_country' => 'ESP',
            'birth_date' => '1985-12-12',
            'physical_fitness' => 'Apto'
        ]);

        $this->reservation = Reservation::create([
            'user_id' => $this->client->id,
            'passenger_id' => $pax->id,
            'status' => 'Confirmada',
            'payment_status' => 'paid',
            'total_price' => 12000,
            'id_locator' => 'PAY-TEST'
        ]);
    }

    #[Test]
    public function el_gestor_puede_ver_los_pagos_de_sus_clientes()
    {
        Livewire::actingAs($this->gestor)
            ->test(GestorPayments::class)
            ->assertSee('PAY-TEST');
    }

    #[Test]
    public function el_gestor_puede_abrir_el_modal_de_reembolso()
    {
        Livewire::actingAs($this->gestor)
            ->test(GestorPayments::class)
            ->call('requestRefund', $this->reservation->id)
            ->assertSet('showRefundModal', true);
    }

    #[Test]
    public function el_gestor_puede_generar_un_link_de_pago_y_ver_el_modal()
    {
        Livewire::actingAs($this->gestor)
            ->test(GestorPayments::class)
            ->call('generatePaymentLink', 'group-123', 3500, $this->client->id)
            ->assertSet('showPaymentLinkModal', true)
            ->assertSee('Link de Pago Generado');
    }
}
