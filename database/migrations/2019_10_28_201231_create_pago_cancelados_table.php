<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoCanceladosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago__cancelados', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->date('fecha_pago');
            $table->decimal('monto', 7, 0);
            $table->integer('usuario_registra_id');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pago_cancelados', function (Blueprint $table) {
            if (Schema::hasColumn('pago_cancelados', 'usuario_id')) {
                $table->dropForeign('pago_cancelados_usuario_id_foreign');
                $table->dropColumn('usuario_id');
            }
        });
        Schema::dropIfExists('pago_cancelados');
    }
}
