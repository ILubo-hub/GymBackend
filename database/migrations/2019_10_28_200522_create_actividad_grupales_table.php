<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadGrupalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad__grupals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('servicio_id');
            $table->date('fecha');
            $table->integer('hora_inicial');
            $table->integer('hora_final');
            $table->integer('cupo');
            $table->softDeletes();
            $table->timestamps();

            //Llaves fóraneas
            $table->foreign('servicio_id')->references('id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actividad_grupales', function (Blueprint $table) {
            if (Schema::hasColumn('actividad_grupales', 'servicio_id')) {
                $table->dropForeign('actividad_grupales_servicio_id_foreign');
                $table->dropColumn('servicio_id');
            }
        });
        Schema::dropIfExists('actividad_grupales');
    }
}
