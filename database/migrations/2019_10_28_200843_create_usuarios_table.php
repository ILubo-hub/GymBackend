<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // nombre, correo electrónico, contraseña y tipo de usuario
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('numero_cedula')->nullable();;
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('email');
            $table->unsignedInteger('tipo_usuario_id');
            $table->string('sexo')->nullable();
            $table->string('telefono')->nullable();;
            $table->date('fecha_nacimiento')->nullable();;
            $table->string('password');
            $table->integer('activo');
            $table->softDeletes();
            $table->timestamps();

            //Llaves fóraneas
            $table->foreign('tipo_usuario_id')->references('id')->on('tipo__usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            if (Schema::hasColumn('usuarios', 'tipo_usuario_id')) {
                $table->dropForeign('usuarios_tipo_usuario_id_foreign');
                $table->dropColumn('tipo_usuario_id');
            }
        });
        Schema::dropIfExists('usuarios');
    }
}
