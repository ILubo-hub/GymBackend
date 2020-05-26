<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('servicio_id');
            $table->unsignedInteger('plan_id');
            $table->timestamps();

            //Llaves fóraneas
            $table->foreign('servicio_id')->references('id')->on('servicios');
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
        Schema::table('plan_servicio', function (Blueprint $table) {
            if (Schema::hasColumn('plan_servicio', 'servicio_id')) {
                $table->dropForeign('plan_servicio_servicio_id_foreign');
                $table->dropColumn('servicio_id');
            }
            if (Schema::hasColumn('plan_servicio', 'plan_id')) {
                $table->dropForeign('plan_servicio_plan_id_foreign');
                $table->dropColumn('plan_id');
            }
        });
        Schema::dropIfExists('plan_servicio');
    }
}
