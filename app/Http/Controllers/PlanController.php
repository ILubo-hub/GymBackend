<?php

namespace App\Http\Controllers;

use App\Historial;
use App\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use JWTAuth;

class PlanController extends Controller
{

    public function __construct()
    {
        //No se quieren proteger todas las acciones
        //Agregar segundo argumento
        $this->middleware('jwt.auth', ['only' => [
            'update', 'store', 'pagar'
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
            $plan = Plan::orderBy('id', 'asc')->with('servicios')->get();
            $respose = [
                'msg' => 'Lista de Planes Registrados',
                'data' => $plan
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function PlanesT()
    {
        try {
            $planesDeleted = Plan::withTrashed()->get();
            $response = [
                'msg' => 'Lista de planes completa',
                'Planes ' => $planesDeleted
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function PlanesBorrados()
    {
        try {
            $planesBorrados = PLan::onlyTrashed()->get();
            $response = [
                'msg' => 'Lista de planes eliminados',
                'data ' => $planesBorrados
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function deleteById($idPlan)
    {
        try {
            $plan = Plan::find($idPlan);
            $plan->delete();
            return 'Plan: ' . $idPlan . ' eliminado temporalmente';
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function pagar(Request $request)
    {
        try {
            //Validar entradas
            $this->validate($request, [
                'usuario_id' => 'required',
                'plan_id' => 'required',
            ]);

            //Obtener usuario autentificado
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msj' => 'Usuario no encontrado'], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        //Crear el objeto a insertar
        $plan = Plan::where('id', $request->plan_id)->first();
        $historial1 = Historial::where('plan_id', $request->plan_id)->where('usuario_id', $request->usuario_id)->where('vigente', 0)->first();
        if($historial1){
            $historial1->vigente=0;
            if($historial1->update()){

                $historial = new Historial();
                $historial->usuario_id = $request->usuario_id;
                $historial->plan_id = $request->plan_id;
                $date = Carbon::now();
                $date2 = Carbon::now();
                $historial->fecha_inicio = $date;
                $historial->fecha_final = $date2->addMonth();
                $historial->vigente = 1;


                if($historial->save()){
                    $response = [
                        'msg' => 'Pago realizado con éxito',
                        'data' => $historial
                    ];
                    return response()->json($response, 200);
                }else{
                    $response = [
                        'msg' => 'Error durante la creación del historial'
                    ];
                    return response()->json($response, 404);
                }
            }else{

                $response = [
                    'msg' => 'Error durante la eliminación del historial viejo',
                    'data' => $historial1
                ];
                return response()->json($response, 404);
            }
        }else{
            $response = [
                'msg' => 'No tiene un historial activo',
                'data' => $historial1
            ];
            return response()->json($response, 403);
        }



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
                'nombre' => 'required|min:3',
                'descripcion' => 'required|min:5',
                'precio' => 'required|numeric',
            ]);

            //Obtener usuario autentificado
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msj' => 'Usuario no encontrado'], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        //Crear el objeto a insertar
        $plan = new Plan();
        $plan->nombre  = $request->input('nombre');
        $plan->descripcion = $request->input('descripcion');
        $plan->precio = $request->input('precio');
        $plan->activo = 1;
        if ($plan->save()) {

            $plan->servicios()->attach($request->input('servicios_id') === null ? [] : $request->input('servicios_id'));
            $plan = $plan->where('id', $plan->id)->with('servicios')->first();
            $response = [
                'msg' => 'Plan creado!',
                'data' => $plan
            ];
            return response()->json($response, 201);
        }
        $response = [
            'msg' => 'Error durante la creación'
        ];
        return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $plan = Plan::where('id', $id)->with('servicios')->first();

            $response = [
                'msg' => 'Plan ingresado',
                'data' => $plan
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function showCli($id)
    {
        try {
            $plan = DB::select("SELECT distinct gymdb.plans.* FROM gymdb.usuarios, gymdb.historials, gymdb.plans  where gymdb.usuarios.id = $id and gymdb.historials.usuario_id and gymdb.historials.vigente and gymdb.historials.plan_id = gymdb.plans.id", [1]);
            $response = [
                'msj' => 'Plan ingresado',
                'data' => $plan
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        //
    }
}
