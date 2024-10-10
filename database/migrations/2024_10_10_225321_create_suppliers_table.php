<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplierDoc')->unique(); // Reemplazamos clientDoc por supplierDoc
            $table->string('supplierName', 50); // Reemplazamos clientName por supplierName
            $table->string('supplierPhone', 20)->nullable(); // Reemplazamos clientPhone por supplierPhone
            $table->string('supplierEmail', 150)->unique()->nullable(); // Reemplazamos clientEmail por supplierEmail
            $table->string('supplierAddress', 150)->nullable(); // Reemplazamos clientAddress por supplierAddress
            $table->text('supplierPhotoUrl')->nullable(); // Reemplazamos clientPhotoUrl por supplierPhotoUrl
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
}
