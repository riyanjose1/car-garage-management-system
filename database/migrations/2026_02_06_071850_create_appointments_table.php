<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();

            $table->date('service_date');
            $table->time('service_time');

            $table->string('service_type'); // e.g., Oil Change, Full Service
            $table->text('notes')->nullable();

            $table->string('status')->default('Pending'); // Pending, Confirmed, Completed, Cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
