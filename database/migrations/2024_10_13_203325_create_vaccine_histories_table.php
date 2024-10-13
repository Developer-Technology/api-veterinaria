<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVaccineHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaccine_histories', function (Blueprint $table) {
            $table->id(); // Llave primaria
            $table->unsignedBigInteger('vaccine_id'); // Relación con la tabla de vacunas
            $table->unsignedBigInteger('pet_id'); // Relación con la tabla de mascotas
            $table->date('vaccine_date'); // Fecha de aplicación de la vacuna
            $table->string('product', 150); // Producto usado
            $table->string('observation', 150); // Observaciones
            $table->timestamps(); // Para los campos created_at y updated_at

            // Definir las llaves foráneas
            $table->foreign('vaccine_id')->references('id')->on('vaccines')->onDelete('cascade');
            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaccine_histories');
    }

}