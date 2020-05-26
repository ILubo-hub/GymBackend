<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use SoftDeletes;

    public function planes()
    {
        return $this->belongsToMany('App\Plan');
    }
    public function actividad_grupales()
    {
        return $this->hasMany('App\Actividad_Grupal');
    }
}
