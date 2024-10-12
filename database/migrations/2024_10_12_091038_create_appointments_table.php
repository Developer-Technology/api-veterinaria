<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade'); // Relación con la tabla de mascotas
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // Relación con la tabla de clientes
            $table->dateTime('appointmentDate'); // Fecha y hora de la cita
            $table->string('reason'); // Motivo de la cita
            $table->enum('status', ['pending', 'confirmed', 'cancelled']); // Estado de la cita

            // Campos adicionales para gestionar las alertas
            $table->boolean('emailAlertSent')->default(false); // Verifica si se ha enviado la alerta por correo
            $table->boolean('whatsappAlertSent')->default(false); // Verifica si se ha enviado la alerta por WhatsApp

            $table->timestamps(); // created_at y updated_at en camelCase
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
    
}