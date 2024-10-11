<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('petCode', 50); // Código de la mascota
            $table->string('petName', 100); // Nombre de la mascota
            $table->date('petBirthDate'); // Fecha de nacimiento de la mascota
            $table->string('petWeight', 10)->nullable(); // Peso de la mascota
            $table->string('petColor', 100); // Color de la mascota
            $table->foreignId('species_id') // Relación con la tabla especies
                ->constrained('species')
                ->onDelete('restrict'); // Restricción para no permitir eliminar especies que tengan mascotas
            $table->foreignId('breeds_id') // Relación con la tabla razas
                ->constrained('breeds')
                ->onDelete('restrict'); // Restricción para no permitir eliminar razas que tengan mascotas
            $table->text('petPhoto')->nullable(); // Foto de la mascota
            $table->string('petGender', 10); // Sexo de la mascota
            $table->string('petAdditional', 200)->nullable(); // Información adicional de la mascota
            $table->foreignId('clients_id') // Relación con la tabla clientes
                ->constrained('clients')
                ->onDelete('cascade'); // Cascada para eliminar mascotas si se elimina un cliente
            $table->timestamps(); // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pets');
    }

}