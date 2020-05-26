<?php

namespace App\Http\Controllers;

use App\Pago_Cancelado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Historial;
use App\Plan;
use Carbon\Carbon;

class PagoCanceladoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        //No se quieren proteger todas las acciones
        //Agregar segundo argumento
        $this->middleware('jwt.auth', ['only' => [
            'index', 'store'
        ]]);
    }
    public function index()
    {
        try {
            $pago = Pago_Cancelado::orderBy('id', 'asc')->with('usuario')->get();
            $respose = [
                'msg' => 'Pagos Cancelados',
                'data' => $pago
            ];


            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function pend()
    {

        $actividades = DB::select("SELECT gymdb.usuarios.nombre, gymdb.usuarios.email, gymdb.historials.fecha_final, gymdb.plans.nombre as nombre_plan, gymdb.plans.precio from gymdb.usuarios, gymdb.historials, gymdb.plans where gymdb.historials.plan_id = gymdb.plans.id and gymdb.historials.usuario_id = gymdb.usuarios.id  and  gymdb.historials.fecha_final < CURDATE() and gymdb.historials.vigente = 1", [1]);
        $response = [
            'msj' => 'Pago pendiente',
            'data' => $actividades
        ];
        return response()->json($response, 200);
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
                'usuario_id' => 'required',
                'plan_id' => 'required',
                'usuario_registra_id' => 'required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        $plan = new Plan();
        $plan = Plan::where('id', $request->plan_id)->first();
        $historial1 = Historial::where('plan_id', $request->plan_id)->where('usuario_id', $request->usuario_id)->where('vigente', 1)->first();
        if($historial1){
            $historial1->vigente=0;
            if($historial1->save()){

                $historial = new Historial();
                $historial->usuario_id = $request->usuario_id;
                $historial->plan_id = $request->plan_id;
                $date = Carbon::now();
                $date2 = Carbon::now();
                $historial->fecha_inicio = $date;
                $historial->fecha_final = $date2->addMonth();
                $historial->vigente = 1;


                if($historial->save()){

                    $pago = new Pago_Cancelado();
                    $pago->usuario_id = $request->usuario_id;
                    $date = Carbon::now();
                    $pago->fecha_pago = $date;
                    $pago->monto = $plan->precio;
                    $pago->usuario_registra_id = $request->usuario_registra_id;
                    $pago->save();


                    $response = [
                        'msg' => 'El pago se ha realizado con éxito',
                        'data' => $pago
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




        //Crear objeto a insertar

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pago_Cancelado  $pago_Cancelado
     * @return \Illuminate\Http\Response
     */
    public function show(Pago_Cancelado $pago_Cancelado)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pago_Cancelado  $pago_Cancelado
     * @return \Illuminate\Http\Response
     */
    public function edit(Pago_Cancelado $pago_Cancelado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pago_Cancelado  $pago_Cancelado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pago_Cancelado $pago_Cancelado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pago_Cancelado  $pago_Cancelado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pago_Cancelado $pago_Cancelado)
    {
        //
    }
}
