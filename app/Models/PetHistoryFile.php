<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetHistoryFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_history_id', 
        'file_path', 
        'file_type'
    ];

    // Relación con la historia clínica
    public function petHistory()
    {
        return $this->belongsTo(PetHistory::class, 'pet_history_id');
    }

}