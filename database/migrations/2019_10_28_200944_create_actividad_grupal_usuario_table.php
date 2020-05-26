<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadGrupalUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad__grupal__usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('actividad_grupal_id');
            $table->timestamps();

            //Llaves fóraneas
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('actividad_grupal_id')->references('id')->on('actividad__grupals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actividad_grupal_usuario', function (Blueprint $table) {
            if (Schema::hasColumn('actividad_grupal_usuario', 'usuario_id')) {
                $table->dropForeign('actividad_grupal_usuario_usuario_id_foreign');
                $table->dropColumn('usuario_id');
            }
            if (Schema::hasColumn('actividad_grupal_usuario', 'actividad_grupal_id')) {
                $table->dropForeign('actividad_grupal_usuario_actividad_grupal_id_foreign');
                $table->dropColumn('actividad_grupal_id');
            }
        });
        Schema::dropIfExists('actividad_grupal_usuario');
    }
}
