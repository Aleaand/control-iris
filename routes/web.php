<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\ManageDestinations;
use App\Livewire\Admin\ManageStarships;
use App\Http\Controllers\StripeController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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

