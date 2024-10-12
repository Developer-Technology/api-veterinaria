<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    // Especificamos el nombre de la tabla
    protected $table = 'appointments';

    // Definimos los campos que se pueden asignar de manera masiva
    protected $fillable = [
        'pet_id', 
        'appointmentDate', 
        'reason', 
        'status', 
        'emailAlertSent', 
        'whatsappAlertSent'
    ];

    // Relación con la tabla de pets (Mascotas)
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

}