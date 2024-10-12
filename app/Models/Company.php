<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'companies';

    // Campos que se pueden llenar mediante asignación masiva (mass assignment)
    protected $fillable = [
        'companyDoc', 
        'companyName', 
        'companyAddress', 
        'companyPhone', 
        'companyEmail', 
        'companyPhoto', 
        'companyCurrency', 
        'companyTax'
    ];

    // Si no necesitas los campos created_at y updated_at
    public $timestamps = true; // Puedes poner false si no necesitas esos campos.

}