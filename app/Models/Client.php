<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'clientDoc',
        'clientName',
        'clientGender',
        'clientPhone',
        'clientEmail',
        'clientAddress',
        'clientPhotoUrl'
    ];

    // Relación uno a muchos con Pet
    public function pets()
    {
        return $this->hasMany(Pet::class, 'clients_id', 'id');
    }

}