<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pago_Cancelado extends Model
{
    public function usuario()
    {
        return $this->belongsTo('App\Usuario');
    }
}
