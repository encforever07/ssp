<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class CatPago extends Model
{
    protected $table='cat_pagos';

    public function especialidades(){
        return $this->hasMany('App\models\Especialidad');
    }
}
