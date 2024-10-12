<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('companyDoc', 50);         // empresaDoc -> company_doc
            $table->string('companyName', 100);       // empresaNombre -> company_name
            $table->string('companyAddress', 200);    // empresaDireccion -> company_address
            $table->string('companyPhone', 20);       // empresaTelefono -> company_phone
            $table->string('companyEmail', 100);      // empresaCorreo -> company_email
            $table->text('companyPhoto')->nullable(); // empresaFoto -> company_photo
            $table->string('companyCurrency', 10);    // empresaMoneda -> company_currency
            $table->decimal('companyTax', 10, 2);     // empresaImpuesto -> company_tax
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
        Schema::dropIfExists('companies');
    }

}