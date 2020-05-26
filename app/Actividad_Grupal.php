<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actividad_Grupal extends Model
{
    use SoftDeletes;

    public function usuarios()
    {
        return $this->belongsToMany('App\Usuario');
    }
    public function servicio()
    {
        return $this->belongsTo('App\Servicio');
    }
}
