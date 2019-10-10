<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    protected $table='obras';

    protected $fillable = [
        'nombre','nomenclatura'
    ];
}
