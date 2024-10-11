<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $table = 'pets';

    protected $fillable = [
        'petCode',
        'petName',
        'petBirthDate',
        'petWeight',
        'petColor',
        'species_id',
        'breeds_id',
        'petPhoto',
        'petGender',
        'petAdditional',
        'clients_id',
    ];

    /**
     * Relación con la especie (Specie).
     */
    public function species()
    {
        return $this->belongsTo(Specie::class, 'species_id', 'id');
    }

    /**
     * Relación con la raza (Breed).
     */
    public function breed()
    {
        return $this->belongsTo(Breed::class, 'breeds_id', 'id');
    }

    /**
     * Relación con el cliente (Client).
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'clients_id', 'id');
    }

}