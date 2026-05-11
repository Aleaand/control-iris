<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tablas de Sistema
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('primarylastname')->nullable();
            $table->string('secondarylastname')->nullable();
            $table->string('role')->default('cliente');
            $table->date('birth_date')->nullable();
            $table->foreignId('assigned_manager_id')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('must_change_password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // 2. Tablas Maestras del Negocio
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('distance_au', 12, 4);
            $table->decimal('max_distance_au', 8, 2)->nullable();
            $table->decimal('launch_fee', 16, 2)->default(0);
            $table->decimal('landing_fee', 16, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->decimal('transport_price', 10, 2)->default(0.00);
            $table->string('country_code', 3)->nullable();
            $table->timestamps();
        });

        Schema::create('starships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('general_capacity');
            $table->integer('vip_capacity');
            $table->string('status')->default('active');
            $table->timestamp('maintenance_start_date')->nullable();
            $table->timestamp('maintenance_end_date')->nullable();
            $table->foreignId('current_location_id')->nullable();
            $table->decimal('operational_cost_per_au', 16, 2)->default(0);
            $table->integer('crew_capacity')->default(0);
            $table->decimal('cruise_speed_au', 8, 4)->default(0);
            $table->decimal('crew_hourly_rate', 16, 2)->default(0);
            $table->decimal('crew_daily_rate', 16, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Operativa y Logística
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_code')->unique();
            $table->foreignId('starship_id')->constrained();
            $table->foreignId('destination_id')->constrained();
            $table->foreignId('origin_id')->nullable()->constrained('destinations');
            $table->timestamp('departure_date');
            $table->timestamp('arrival_date');
            $table->decimal('base_price', 16, 2);
            $table->integer('booked_passengers')->default(0);
            $table->string('status')->default('scheduled');
            $table->decimal('au_distance', 12, 2)->default(0);
            $table->integer('total_capacity')->default(0);
            $table->decimal('operational_cost', 16, 2)->default(0);
            $table->decimal('mission_speed_au', 8, 4)->default(0);
            $table->decimal('crew_hourly_rate', 16, 2)->default(0);
            $table->decimal('crew_daily_rate', 16, 2)->default(0);
            $table->decimal('launch_cost_earth', 16, 2)->default(0);
            $table->decimal('launch_cost_planet', 16, 2)->default(0);
            $table->decimal('landing_cost_earth', 16, 2)->default(0);
            $table->decimal('landing_cost_planet', 16, 2)->default(0);
            $table->timestamp('return_departure_date')->nullable();
            $table->decimal('return_base_price', 16, 2)->nullable();
            $table->decimal('mission_profitability', 16, 2)->default(0);
            $table->foreignId('price_updated_by')->nullable();
            $table->timestamp('price_updated_at')->nullable();
            $table->decimal('previous_base_price', 16, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Iris Pre-Launch Manor');
            $table->foreignId('location_id')->constrained();
            $table->integer('galactic_stars')->default(0);
            $table->decimal('price_per_night', 10, 2)->default(0);
            $table->integer('total_rooms')->default(0);
            $table->foreignId('price_updated_by')->nullable();
            $table->timestamp('price_updated_at')->nullable();
            $table->decimal('previous_price_per_night', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('terrestrial_flights', function (Blueprint $table) {
            $table->id();
            $table->string('airline');
            $table->string('flight_number')->nullable();
            $table->foreignId('origin_id')->constrained('locations');
            $table->foreignId('destination_id')->constrained('locations');
            $table->timestamp('departure_datetime');
            $table->timestamp('arrival_datetime')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('baggage_price', 10, 2)->default(0);
            $table->integer('executive_capacity')->default(20);
            $table->string('status')->default('Programado');
            $table->foreignId('price_updated_by')->nullable();
            $table->timestamp('price_updated_at')->nullable();
            $table->decimal('previous_price', 10, 2)->nullable();
            $table->timestamps();
        });

        // 4. Pasajeros y Documentación
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_number');
            $table->string('document_country', 3);
            $table->string('name');
            $table->string('primarylastname')->nullable();
            $table->string('secondarylastname')->nullable();
            $table->date('birth_date');
            $table->string('blood_type')->nullable();
            $table->text('allergies')->nullable();
            $table->string('physical_fitness')->default('No apto');
            $table->string('iris_passport_number')->nullable();
            $table->date('iris_passport_expiration')->nullable();
            $table->date('training_certificate_date')->nullable();
            $table->string('training_certificate_status')->nullable();
            $table->string('passport_photo')->nullable();
            $table->string('passport_status')->default('none');
            $table->string('passport_pdf')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['document_number', 'document_country']);
        });

        Schema::create('passports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('number');
            $table->date('expiration_date');
            $table->boolean('is_valid')->default(true);
            $table->timestamps();
        });

        Schema::create('medical_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('issue_date');
            $table->string('status')->default('Apto');
            $table->timestamps();
        });

        // 5. Reservas y Finanzas
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_locator')->index();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('passenger_id')->nullable();
            $table->foreignId('space_flight_id')->nullable();
            $table->uuid('booking_group_id')->nullable()->index();
            $table->string('group_name')->nullable();
            $table->string('seat_type')->nullable();
            $table->string('seat_number')->nullable();
            $table->decimal('total_price', 15, 2)->default(0);
            $table->boolean('discount_applied')->default(false);
            $table->string('status')->default('Pendiente');
            $table->string('payment_status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('stripe_session_id')->nullable()->index();
            $table->string('stripe_receipt_url', 500)->nullable();
            $table->json('stripe_receipts')->nullable();
            $table->json('price_snapshot')->nullable();
            $table->string('manual_adjustment_type')->default('none');
            $table->decimal('manual_adjustment_value', 15, 2)->default(0);
            $table->string('discount_note')->nullable();
            $table->boolean('is_adenda')->default(false);
            $table->foreignId('parent_reservation_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reservation_logistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('terrestrial_flight_id')->nullable();
            $table->foreignId('hotel_id')->nullable();
            $table->integer('hotel_nights')->default(0);
            $table->boolean('training_included')->default(false);
            $table->boolean('vip_transfer_included')->default(false);
            $table->boolean('refund_insurance_included')->default(false);
            $table->boolean('passport_management_included')->default(false);
            $table->timestamps();
        });

        Schema::create('price_logs', function (Blueprint $table) {
            $table->id();
            $table->string('item_type');
            $table->bigInteger('item_id');
            $table->decimal('old_price', 16, 2);
            $table->decimal('new_price', 16, 2);
            $table->string('reason')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->index(['item_type', 'item_id']);
        });

        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('gestor_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('Pendiente');
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->decimal('penalty_amount', 12, 2)->nullable();
            $table->text('gestor_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('has_insurance')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('booking_group_id');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('client_id')->constrained('users');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('activo');
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        // 6. Agenda y Tareas
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_gestor_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('general');
            $table->string('status')->default('Pendiente');
            $table->string('priority')->default('media');
            $table->json('payload')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gestor_id')->constrained('users')->onDelete('cascade');
            $table->string('type')->default('nota');
            $table->string('zoom_link')->nullable();
            $table->text('notes');
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->nullable()->constrained()->onDelete('set null');
            $table->string('reference')->nullable();
            $table->string('category');
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->timestamp('expense_date');
            $table->timestamps();
        });

        Schema::create('operational_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('starship_id')->constrained()->onDelete('cascade');
            $table->decimal('old_cost_per_au', 10, 2)->default(0);
            $table->decimal('new_cost_per_au', 10, 2);
            $table->string('reason')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_costs');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('contact_logs');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('payment_links');
        Schema::dropIfExists('refund_requests');
        Schema::dropIfExists('price_logs');
        Schema::dropIfExists('reservation_logistics');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('medical_certificates');
        Schema::dropIfExists('passports');
        Schema::dropIfExists('passengers');
        Schema::dropIfExists('terrestrial_flights');
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('flights');
        Schema::dropIfExists('starships');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('destinations');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
