<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunicate extends Model
{
    protected $table = 'comunicate';

    protected $fillable = [
        'asunto',
        'descripcion',
        'user_id'
    ];
}
