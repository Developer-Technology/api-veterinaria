<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccineHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'vaccine_id', 
        'pet_id', 
        'vaccine_date', 
        'product', 
        'observation'
    ];

    /**
     * Relación con la vacuna
     * Una entrada de VaccineHistory pertenece a una vacuna.
     */
    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'vaccine_id', 'id');
    }

    /**
     * Relación con la mascota (Pet)
     * Una entrada de VaccineHistory pertenece a una mascota.
     */
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

}