<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaCambio extends Model
{
    protected $table = 'tasa_cambio';

    protected $fillable = [
        'Nombre',
        'simbolo',
        'Cambio'
    ];
}
