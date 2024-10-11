<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetNote extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'pet_notes';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'pet_id',
        'noteDescription',
        'noteDate',
    ];

    /**
     * Relación con la tabla de pets.
     * Un registro de pet_notes pertenece a una mascota (pet).
     */
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

}