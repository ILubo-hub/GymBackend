<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historials', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('plan_id');
            $table->date('fecha_inicio');
            $table->date('fecha_final');
            $table->integer('vigente');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('plan_id')->references('id')->on('plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historiales', function (Blueprint $table) {
            if (Schema::hasColumn('historiales', 'usuario_id')) {
                $table->dropForeign('historiales_usuario_id_foreign');
                $table->dropColumn('usuario_id');
            }
            if (Schema::hasColumn('historiales', 'plan_id')) {
                $table->dropForeign('historiales_plan_id_foreign');
                $table->dropColumn('plan_id');
            }
        });
        Schema::dropIfExists('historiales');
    }
}
