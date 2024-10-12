<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'pet_id', 
        'client_id', // Añadido el cliente
        'appointmentDate', 
        'reason', 
        'status', 
        'emailAlertSent', 
        'whatsappAlertSent'
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id'); // Relación con clientes
    }

}