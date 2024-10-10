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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('clientDoc')->unique();
            $table->string('clientName', 50);
            $table->string('clientGender', 5)->nullable();
            $table->string('clientPhone', 20)->nullable();
            $table->string('clientEmail', 150)->unique()->nullable();
            $table->string('clientAddress', 150)->nullable();
            $table->text('clientPhotoUrl')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
