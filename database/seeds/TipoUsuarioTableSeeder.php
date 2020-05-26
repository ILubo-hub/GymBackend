<?php

use Illuminate\Database\Seeder;

class TipoUsuarioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //1
        $tipo = new \App\Tipo_Usuario([
            'descripcion' => "Administrador"
        ]);
        $tipo->save();

        //2
        $tipo = new \App\Tipo_Usuario([
            'descripcion' => "Empleado"
        ]);
        $tipo->save();

        //3
        $tipo = new \App\Tipo_Usuario([
            'descripcion' => "Cliente"
        ]);
        $tipo->save();
    }
}
