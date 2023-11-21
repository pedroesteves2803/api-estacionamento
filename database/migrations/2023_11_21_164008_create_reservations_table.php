<?php

use App\Models\Vacancies;
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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_id')->constrained(Parking::class);
            $table->foreignId('vacancy_id')->constrained(Vacancies::class);
            $table->foreignId('device_id')->constrained(User::class);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', [0, 1, 2]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
