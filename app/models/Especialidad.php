<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table='especialidades';

    protected $fillable = [
        'nombre','sigla','cat_pago_id'
    ];

    public function cat_pago(){
        return $this->belongsTo('App\models\Catpago');
    }
}
