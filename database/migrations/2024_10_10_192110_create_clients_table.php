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
            $table->integer('clientDoc')->length(12); // DNI or Cedula
            $table->string('clientName', 50)->nullable(); // Nombre
            $table->string('clientGender', 5)->nullable(); // Genero
            $table->string('clientPhone', 20)->nullable(); // Teléfono
            $table->string('clientEmail', 150)->nullable(); // Correo
            $table->string('clientAddress', 150)->nullable(); // Domicilio
            $table->text('clientPhotoUrl')->nullable(); // Foto URL
            $table->timestamps(); // created_at and updated_at
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
