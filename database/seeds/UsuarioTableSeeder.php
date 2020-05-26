<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsuarioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();
        $usuario = new \App\Usuario([
            'numero_cedula' => "207940151",
            'nombre' => "Jonathan",
            'apellidos' => "Morera García",
            'email' => "jona506@Outlook.com",
            'tipo_usuario_id' => 1,
            'sexo' => "Masculino",
            'telefono' => "86293334",
            'fecha_nacimiento' => $date->subYear(20),
            'password' => bcrypt(123456),
            'activo' => 1
        ]);
        $usuario->save();


        $usuario = new \App\Usuario([
            'numero_cedula' => "208190951",
            'nombre' => "Maureen",
            'apellidos' => "Chacón Alvarez",
            'email' => "maureen@gmail.com",
            'tipo_usuario_id' => 2,
            'sexo' => "Femenino",
            'telefono' => "61061302",
            'fecha_nacimiento' => $date->subYear(18),
            'password' => bcrypt(123456),
            'activo' => 1
        ]);
        $usuario->save();
    }
}
