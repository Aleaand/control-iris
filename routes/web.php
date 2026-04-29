<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\ManageDestinations;
use App\Livewire\Admin\ManageStarships;
use App\Http\Controllers\StripeController;

Route::view('/', 'welcome');

Route::get('dashboard', function () {
    $user = auth()->user();
    
    if ($user->role === 'super_admin') {
        return redirect()->route('admin.dashboard');
    }
    
    if ($user->role === 'gestor') {
        return redirect()->route('gestor.dashboard');
    }
    
    // Default or clients
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/dashboard', \App\Livewire\Admin\AdminDashboard::class)->name('admin.dashboard');
    Route::get('admin/destinations', ManageDestinations::class)->name('admin.destinations');
    Route::get('admin/starships', ManageStarships::class)->name('admin.starships');
    Route::get('/admin/flights', \App\Livewire\Admin\ManageFlights::class)->name('admin.flights');
    Route::get('/admin/hotels', \App\Livewire\Admin\ManageHotels::class)->name('admin.hotels');
    Route::get('/admin/terrestrial-flights', \App\Livewire\Admin\ManageTerrestrialFlights::class)->name('admin.terrestrial-flights');
    Route::get('/admin/users/{role}', \App\Livewire\Admin\ManageUsers::class)->name('admin.users.role');
    Route::get('/admin/passengers/{userId?}', \App\Livewire\Admin\ManagePassengers::class)->name('admin.passengers');
    Route::get('/admin/reservations', \App\Livewire\Admin\ManageReservations::class)->name('admin.reservations');
    Route::get('/admin/reservations/{reservation}/ticket', function (\App\Models\Reservation $reservation) {
        return view('admin.reservation-ticket', ['res' => $reservation->load(['user', 'spaceFlight.destination', 'logistics.hotel', 'logistics.terrestrialFlight', 'logistics.terrestrialFlight.originLocation', 'logistics.terrestrialFlight.destinationLocation'])]);
    })->name('admin.reservations.ticket');
    Route::get('/admin/finances', \App\Livewire\Admin\FinancialDashboard::class)->name('admin.finances');
    Route::get('/admin/tariffs', \App\Livewire\Admin\ManageTariffs::class)->name('admin.tariffs');

    // Stripe redirect callbacks (authenticated)
    Route::get('/admin/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/admin/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
});

// Stripe Webhook — no auth, no CSRF (excluded in bootstrap/app.php)
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

// ── Panel del Gestor ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:gestor'])->prefix('gestor')->name('gestor.')->group(function () {

    // Cambio de contraseña (primer login — no requiere contraseña ya cambiada)
    Route::get('/set-password',  \App\Livewire\Gestor\SetPassword::class)->name('set-password');

    // Resto de rutas protegidas por must_change_password
    Route::middleware('ensure_password_changed')->group(function () {
        Route::get('/dashboard',     \App\Livewire\Gestor\GestorDashboard::class)->name('dashboard');
        Route::get('/clients',       \App\Livewire\Gestor\GestorClients::class)->name('clients');
        Route::get('/reservations',  \App\Livewire\Gestor\GestorReservations::class)->name('reservations');
        Route::get('/compliance',    \App\Livewire\Gestor\GestorCompliance::class)->name('compliance');
        Route::get('/payments',      \App\Livewire\Gestor\GestorPayments::class)->name('payments');
        Route::get('/radar',         \App\Livewire\Gestor\GestorRadar::class)->name('radar');
        Route::get('/missions',      \App\Livewire\Gestor\GestorMissions::class)->name('missions');
        Route::get('/communication', \App\Livewire\Gestor\GestorCommunication::class)->name('communication');

        // PDF del ticket (Final GO)
        Route::get('/reservations/{reservation}/ticket-pdf', [\App\Http\Controllers\GestorController::class, 'downloadTicket'])
            ->name('reservations.ticket-pdf');
    });
});
