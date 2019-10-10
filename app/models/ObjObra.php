<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ObjObra extends Model
{
    protected $table='objobras';

    protected $fillable = [
        'nombre','codigo'
    ];
}
