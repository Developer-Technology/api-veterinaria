<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breeds', function (Blueprint $table) {
            $table->id(); // ID autoincremental
            $table->string('breedName', 100); // Campo para el nombre de la raza
            $table->unsignedBigInteger('species_id'); // ID foráneo de la especie

            // Clave foránea con relación a la tabla species
            $table->foreign('species_id')->references('id')->on('species')->onDelete('cascade');

            $table->timestamps(); // Timestamps para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('breeds');
    }

}