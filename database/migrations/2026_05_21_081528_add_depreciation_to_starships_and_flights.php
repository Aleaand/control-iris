<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('starships', function (Blueprint $table) {
            $table->decimal('depreciation_per_au', 10, 2)->default(0.00);
        });

        Schema::table('flights', function (Blueprint $table) {
            $table->decimal('flight_depreciation', 10, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('starships', function (Blueprint $table) {
            $table->dropColumn('depreciation_per_au');
        });

        Schema::table('flights', function (Blueprint $table) {
            $table->dropColumn('flight_depreciation');
        });
    }
};
