<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');  // Relación con la tabla pets
            $table->string('noteDescription', 140);  // Descripción de la nota
            $table->date('noteDate');  // Fecha de la nota
            $table->timestamps();

            // Llave foránea para la relación con pets
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
        Schema::dropIfExists('pet_notes');
    }

}