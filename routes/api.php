<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'gym'], function () {
        Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
            Route::post('login', 'AuthController@login');
            Route::post('logout', 'AuthController@logout');
            Route::post('refresh', 'AuthController@refresh');
            Route::post('register', 'AuthController@register');
            Route::post('me', 'AuthController@me');
        });
        Route::group(['middleware' => 'api', 'prefix' => 'user'], function ($router) {
            Route::get('', 'UsuarioController@index');
            Route::get('all', 'UsuarioController@UsuariosT');
            Route::get('trashed', 'UsuarioController@UsuariosBorrados');
            Route::get('cli', 'UsuarioController@clientv3');
            Route::get('shid/{id}', 'UsuarioController@showID');
            Route::get('usu/{id}', 'UsuarioController@index2');
            Route::post('act/{id}', 'UsuarioController@update');
            Route::patch('actsr/{id}', 'UsuarioController@updateUser');
            Route::post('show/{filtro}', 'UsuarioController@show');
            Route::post('delete/{id}', 'UsuarioController@deleteById');
            Route::post('rest/{id}', 'UsuarioController@restoreById');
        });
        Route::group(['middleware' => 'api', 'prefix' => 'serv'], function ($router) {
            Route::get('', 'ServicioController@index');
            Route::get('all', 'ServicioController@ServiciosT');
            Route::get('trashed', 'ServicioController@ServiciosBorrados');
            Route::post('store', 'ServicioController@store');
            Route::get('grp', 'ServicioController@grupales');
            Route::post('delete', 'ServicioController@deleteById');
            Route::post('restore', 'ServicioController@restoreById');
            Route::post('show/{filtro}', 'ServicioController@show');
            Route::get('shid/{id}', 'ServicioController@showID');
            Route::get('shidtr/{id}', 'ServicioController@showIDTrashed');
        });
        Route::group(['middleware' => 'api', 'prefix' => 'plan'], function ($router) {
            Route::get('', 'PlanController@index');
            Route::post('all', 'PlanController@PlanesT');
            Route::post('trashed', 'PlanController@PlanesBorrados');
            Route::post('store', 'PlanController@store');
            Route::post('pay', 'PlanController@pagar');
            Route::get('show/{id}', 'PlanController@show');
            Route::get('showcli/{id}', 'PlanController@showCli');
            Route::post('delete/{id}', 'PlanController@deleteById');
        });
        Route::group(['middleware' => 'api', 'prefix' => 'acgr'], function ($router) {
            Route::get('', 'ActividadGrupalController@index');
            Route::post('in', 'ActividadGrupalController@getin');
            Route::post('out', 'ActividadGrupalController@getout');
            Route::get('all', 'ActividadGrupalController@actividadUsuario');
            Route::post('store', 'ActividadGrupalController@store');
            Route::get('ac/{id}', 'ActividadGrupalController@index2');
            Route::get('actv/{id}', 'ActividadGrupalController@filtrarID');

        });
        Route::group(['middleware' => 'api', 'prefix' => 'pg'], function ($router) {
            Route::get('', 'PagoCanceladoController@index');
            Route::get('pend', 'PagoCanceladoController@pend');
            Route::post('pay', 'PagoCanceladoController@store');
            Route::post('cd/{$id}', 'PagoCanceladoController@listaPagos');

        });
        Route::group(['middleware' => 'api', 'prefix' => 'type'], function ($router) {
            Route::post('', 'TipoUsuarioController@index');
            Route::post('store', 'TipoUsuarioController@store');
        });
    });
});
