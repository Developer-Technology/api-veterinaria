<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specie extends Model
{
    use HasFactory;

    protected $table = 'species';

    protected $fillable = [
        'specieName'
    ];

    // Relación uno a muchos con Breed
    public function breeds()
    {
        return $this->hasMany(Breed::class, 'species_id', 'id');
    }
    
    // Relación uno a muchos con Pet
    public function pets()
    {
        return $this->hasMany(Pet::class, 'species_id', 'id');
    }

    // Relación uno a muchos con Vaccine
    public function vaccines()
    {
        return $this->hasMany(Vaccine::class, 'species_id', 'id');
    }

}