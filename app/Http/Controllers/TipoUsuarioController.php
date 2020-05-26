<?php

namespace App\Http\Controllers;

use App\Tipo_Usuario;
use Illuminate\Http\Request;

class TipoUsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $tipo = Tipo_Usuario::orderBy('id', 'asc')->get();
            $respose = [
                'msg' => 'Lista de Tipos Registrados',
                'data' => $tipo
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            //Validar entradas
            $this->validate($request, [
                'descripcion' => 'required',
            ]);

            //Obtener usuario autentificado
            //if (!$user = JWTAuth::parseToken()->authenticate()) {
            //    return response()->json(['msj' => 'Usuario no encontrado'], 404);
            //}
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        //Crear el objeto a insertar
        $tipo = new Tipo_Usuario([
            'descripcion' => $request->input('descripcion')
        ]);
        if ($tipo->save()) {
            $response = [
                'msg' => 'Tipo creado!',
                'data' => $tipo
            ];
            return response()->json($response, 201);
        }
        $reponse = [
            'msg' => 'Error durante la creación'
        ];
        return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tipo_Usuario  $tipo_Usuario
     * @return \Illuminate\Http\Response
     */
    public function show(Tipo_Usuario $tipo_Usuario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tipo_Usuario  $tipo_Usuario
     * @return \Illuminate\Http\Response
     */
    public function edit(Tipo_Usuario $tipo_Usuario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tipo_Usuario  $tipo_Usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tipo_Usuario $tipo_Usuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tipo_Usuario  $tipo_Usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tipo_Usuario $tipo_Usuario)
    {
        //
    }
}
