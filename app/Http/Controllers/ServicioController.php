<?php

namespace App\Http\Controllers;

use App\Servicio;
use Illuminate\Http\Request;
use JWTAuth;

class ServicioController extends Controller
{

    public function __construct()
    {
        //No se quieren proteger todas las acciones
        //Agregar segundo argumento
        $this->middleware('jwt.auth', ['only' => [
            'update', 'deleteById', 'show', 'restoreById', 'store'
        ]]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $servicios = Servicio::orderBy('id', 'asc')->with('actividad_grupales')->get();
            $respose = [
                'msg' => 'Lista de Servicios Registrados',
                'data' => $servicios
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function ServiciosT()
    {
        try {
            $serviciosDeleted = Servicio::withTrashed()->get();
            $response = [
                'msg' => 'Lista de servicios completa',
                'data ' => $serviciosDeleted
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }


    public function ServiciosBorrados()
    {
        try {
            $serviciosBorrados = Servicio::onlyTrashed()->get();
            $response = [
                'msj' => 'Lista de servicios eliminados',
                'data' => $serviciosBorrados
            ];
            return response()->json($response, 200);
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
        //https://laravel.com/docs/6.x/validation#available-validation-rules
        try {

            $this->validate($request, [
                'nombre' => 'required|min:3',
                'descripcion' => 'required|min:5',
                'tipo_servicio' => 'required',
            ]);
            //Obtener el usuario autentificado actual
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }

        $servicio = new Servicio();
        $servicio->nombre = $request->nombre;
        $servicio->descripcion = $request->descripcion;
        $servicio->tipo_servicio = $request->tipo_servicio;
        $servicio->activo = 1;
        if ($servicio->save()) {
            $response = [
                'msg' => 'Servicio creado!',
                'data' => $servicio
            ];
            return response()->json($response, 201);
        } else {
            $response = [
                'msg' => 'Error durante la creación'
            ];
            return response()->json($response, 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function show($filtro)
    {
        try {
            if ($filtro == 1) {
                $servicios = Servicio::orderBy('id', 'asc')->get();
                $respose = [
                    'msg' => 'Lista de Servicios Registrados',
                    'data' => $servicios
                ];
                return response()->json($respose, 200);
            } else {
                if ($filtro == 2) {
                    $serviciosBorrados = Servicio::onlyTrashed()->get();
                    $response = [
                        'msg' => 'Lista de servicios eliminados',
                        'data ' => $serviciosBorrados
                    ];
                    return response()->json($response, 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function showID($id)
    {
        try {
            $servicio = Servicio::orderBy('id', 'asc')->where('id', $id)->first();
            $respose = [
                'msg' => 'Servicios Seleccionado',
                'data' => $servicio
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function showIDTrashed($id)
    {
        try {
            $servicio = Servicio::onlyTrashed()->where('id', $id)->first();
            $respose = [
                'msg' => 'Servicios Seleccionado borrado',
                'data' => $servicio
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }


    public function grupales()
    {
        try {
            $servicios = Servicio::orderBy('id', 'asc')->where('tipo_servicio', "Grupal")->get();
            $respose = [
                'msg' => 'Lista de Servicios Registrados',
                'data' => $servicios
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function deleteById(Request $request)
    {
        $servicio = Servicio::find($request->id);
        if($servicio->delete()){
            $respose = [
                'msg' => 'Servicio Eliminado',
                'data' => $servicio
            ];
            return response()->json($respose, 200);
        }else{
            $respose = [
                'msg' => 'Error al crear servicio',
                'data' => $servicio
            ];
            return response()->json($respose, 404);
        }
    }

    public function restoreById(Request $request)
    {
        try {
            $serviciosBorrados = Servicio::withTrashed()->findOrFail($request->id)->restore();
            $response = [
                'msj' => 'Servicio restaurado',
                'data ' => $serviciosBorrados
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function responseErrors($errors, $statusHTML)
    {
        $transformed = [];

        foreach ($errors as $field => $message) {
            $transformed[] = [
                'field' => $field,
                'message' => $message[0]
            ];
        }

        return response()->json([
            'errors' => $transformed
        ], $statusHTML);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function edit(Servicio $servicio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Servicio $servicio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Servicio $servicio)
    {
        //
    }
}
