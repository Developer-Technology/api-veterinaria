<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use HasFactory;

    protected $fillable = ['vaccineName', 'species_id'];

    /**
     * Relación con la especie
     */
    public function species()
    {
        return $this->belongsTo(Specie::class, 'species_id', 'id');
    }

}