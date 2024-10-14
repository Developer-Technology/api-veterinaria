<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_code', 
        'history_date', 
        'history_time', 
        'history_reason', 
        'history_symptoms', 
        'history_diagnosis', 
        'history_treatment', 
        'user_id', 
        'pet_id'
    ];

    // Relación con los archivos de la historia clínica
    public function files()
    {
        return $this->hasMany(PetHistoryFile::class);
    }

    // Relación con la mascota
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id');
    }

    // Relación con el usuario (por ejemplo, el veterinario)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}