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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Link vehicle to the logged-in user
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Vehicle details
            $table->string('brand');
            $table->string('model');
            $table->string('plate_number')->unique();
            $table->year('year')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
