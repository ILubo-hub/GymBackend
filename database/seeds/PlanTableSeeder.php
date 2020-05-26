<?php

use Illuminate\Database\Seeder;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*            $table->increments('id');
            $table->string('nombre');
            $table->string('descripcion');
            $table->decimal('precio', 7, 0);
            $table->integer('activo');*/

        $plan = new \App\Plan([
            'nombre' => "Correr",
            'descripcion' => "Correr 4km por la montaña",
            'precio' => 7500,
            'activo' => 1
        ]);
        $plan->save();
    }
}
