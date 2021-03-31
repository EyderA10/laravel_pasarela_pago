<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenesProducto extends Model
{
    protected $table = 'orden_productos';

    protected $fillable = [
        'producto',
        'descripcion',
        'precio_a',
        'precio_b',
        'imagen',
        'cantidad',
        'orden_id'
    ];

    public function orden()
    {
        return $this->belongsTo('App\Ordene');
    }
}
