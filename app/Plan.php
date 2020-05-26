<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    public function usuarios()
    {
        return $this->belongsToMany('App\Usuario');
    }
    public function servicios()
    {
        return $this->belongsToMany(
            'App\Servicio',
            'plan_servicio', //Tabla intermedia
            'plan_id', //Llave fóranea del modelo actual
            'servicio_id' // Llave fóranea que referencia
        );
    }
}
