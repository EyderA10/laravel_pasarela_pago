<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ordene extends Model
{
    protected $table = 'ordenes';

    protected $fillable = [
        'tienda_id',
        'user_id',
        'state',
        'lat',
        'lon',
        'type',
        'nombre_recepcion',
        'num_cel_recepcion',
        'hora'
    ];

    public function ordenProd(){
        return $this->hasMany('App\OrdenesProducto');
    }
}
