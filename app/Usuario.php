<?php

namespace App;


use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable implements JWTSubject
{
    use SoftDeletes;
    use Notifiable;



    public function actividad_grupales()
    {
        return $this->belongsToMany(
            'App\Actividad_grupal',
            'actividad__grupal__usuarios', //Tabla intermedia
            'usuario_id', //Llave fóranea del modelo actual
            'actividad_grupal_id' // Llave fóranea que referencia
        );
    }
    public function historiales()
    {
        return $this->belongsToMany(
            'App\Plan',
            'historials', //Tabla intermedia
            'usuario_id', //Llave fóranea del modelo actual
            'plan_id' // Llave fóranea que referencia
        );
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function tipo_usuario()
    {
        return $this->belongsTo('App\Tipo_Usuario');
    }
}
