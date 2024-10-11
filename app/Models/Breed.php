<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    use HasFactory;

    protected $fillable = ['breedName', 'species_id'];

    /**
     * Relación con la especie
     */
    public function species()
    {
        return $this->belongsTo(Specie::class, 'species_id', 'id');
    }

    // Relación uno a muchos con Pet
    public function pets()
    {
        return $this->hasMany(Pet::class, 'breeds_id', 'id');
    }

}