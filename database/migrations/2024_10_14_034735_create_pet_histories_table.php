<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pet_histories', function (Blueprint $table) {
            // Aquí es donde defines las columnas de tu tabla
            $table->id();
            $table->string('history_code', 50);
            $table->date('history_date');
            $table->time('history_time');
            $table->string('history_reason', 100);
            $table->string('history_symptoms', 350);
            $table->string('history_diagnosis', 350);
            $table->string('history_treatment', 350);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pet_id');
            $table->timestamps();

            // Definir claves foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');
        });

        // Tabla separada para almacenar archivos multimedia asociados con las historias clínicas
        Schema::create('pet_history_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_history_id');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->timestamps();

            $table->foreign('pet_history_id')->references('id')->on('pet_histories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pet_history_files');
        Schema::dropIfExists('pet_histories');
    }

}