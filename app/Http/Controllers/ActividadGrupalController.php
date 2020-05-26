<?php

namespace App\Http\Controllers;

use App\Actividad_Grupal;
use App\Actividad_Grupal_Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class ActividadGrupalController extends Controller
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
            'index', 'restoreById', 'store'
        ]]);
    }

    public function index()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            $actividad = Actividad_Grupal::orderBy('id', 'asc')->with('servicio')->get();
            $respose = [
                'msg' => 'Lista de Actividades Registradas',
                'data' => $actividad
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function filtrarID($id)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            $actividad = Actividad_Grupal::where('id', $id)->first();
            $respose = [
                'msg' => 'Actividades Encontrada',
                'data' => $actividad
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function actividadUsuario(){
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            $actividad = Actividad_Grupal::orderBy('id', 'asc')->with('usuarios')->get();
            $respose = [
                'msg' => 'Lista de Actividades Registradas con clientes',
                'data' => $actividad
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function index2($id)
    {

        $actividades = DB::select("SELECT distinct gymdb.actividad__grupals.*, gymdb.plan_servicio.plan_id FROM gymdb.actividad__grupals, gymdb.plan_servicio, gymdb.historials, gymdb.usuarios where gymdb.actividad__grupals.servicio_id = gymdb.plan_servicio.servicio_id and gymdb.plan_servicio.plan_id = gymdb.historials.plan_id and gymdb.historials.usuario_id = $id and gymdb.actividad__grupals.fecha>CURDATE()", [1]);
        $response = [
            'msj' => 'Actividades según el plan',
            'data' => $actividades
        ];
        return response()->json($response, 200);
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
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            //Validar entradas
            $this->validate($request, [
                'servicio_id' => 'required',
                'fecha' => 'required',
                'hora_inicial' => 'required',
                'cupo' => 'required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        $date = Carbon::now();
        $date = date("Y-m-d", strtotime($request->fecha));

        $actividadVal = Actividad_Grupal::where('fecha', $date)->where('hora_inicial', $request->hora_inicial)->first();



        if ($actividadVal === null) {
            //Crear objeto a insertar
            $actividad = new Actividad_Grupal();
            $actividad->servicio_id = $request->servicio_id;
            $actividad->fecha = $date;
            $actividad->hora_inicial = $request->hora_inicial;
            $actividad->hora_final = $request->hora_inicial + 1;
            $actividad->cupo = $request->cupo;
            $actividad->save();

            $response = [
                'msj' => 'Actividad registrada con éxito',
                'data' => $actividad
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'msj' => 'Ya hay una actividad registrada a es hora y en esa fecha',
                'data' => $actividadVal
            ];
            return response()->json($response, 403);
        }
    }



    public function restarCupo($id): int
    {
        $actividad = Actividad_Grupal::find($id);
        $cupo = $actividad->cupo - 1;

        $actividad->cupo = $cupo;


        if ($actividad->save()) {
            return $cupo;
        } else {
            return 0;
        }
    }


    public function sumarCupo($id): int
    {
        $actividad = Actividad_Grupal::find($id);
        $cupo = $actividad->cupo + 1;

        $actividad->cupo = $cupo;


        if ($actividad->save()) {
            return $cupo;
        } else {
            return 0;
        }
    }

    public function getin(Request $request)
    {
        try {
            $this->validate($request, [
                'actividad_grupal_id' => 'required',
                'usuario_id' => 'required',
            ]);

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }



            $actividad = new Actividad_Grupal();
            $actividad = Actividad_Grupal::where('id', $request->input('actividad_grupal_id'))->first();
            $value = Actividad_Grupal_Usuario::where('usuario_id',$request->usuario_id)->where('actividad_grupal_id', $request->actividad_grupal_id)->first();



            if($value){
                $respose = [
                    'msj' => 'Ya está registrado',
                    'data' => $value
                ];
                return response()->json($respose, 403);
            }else{
                if ($actividad->cupo > 0) {
                    $act = new Actividad_Grupal_Usuario();
                    $act->usuario_id = $request->input('usuario_id');
                    $act->actividad_grupal_id = $request->input('actividad_grupal_id');
                    if ($act->save()) {
                        $cupo = $this->restarCupo($actividad->id);
                        $respose = ['msg' => 'Suscripción correcta', 'data' => $act, 'Cupos restantes' =>  $cupo];
                        return response()->json($respose, 200);
                    } else {
                        $respose = [
                            'msg' => 'No se pudo registrar en esta actividad',
                            'data' => $act
                        ];
                        return response()->json($respose, 403);
                    }
                } else {
                    $respose = [
                        'msg' => 'Cupos insuficientes',
                        'data' => $$request
                    ];
                    return response()->json($respose, 403);
                }
            }


        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function getout(Request $request)
    {
        try {
            $this->validate($request, [
                'actividad_grupal_id' => 'required',
                'usuario_id' => 'required',
            ]);

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }

            $actividad = new Actividad_Grupal();
            $actividad = Actividad_Grupal::where('id', $request->input('actividad_grupal_id'))->first();
            $value = Actividad_Grupal_Usuario::where('usuario_id',$request->usuario_id)->where('actividad_grupal_id', $request->actividad_grupal_id)->first();

            $date =Carbon::now();
            $date2 = Carbon::now();
            $dateComp = date("Y-m-d", strtotime($date2));
            $dateFecha = date("Y-m-d", strtotime($actividad->fecha));

            if($value){
                if($dateComp < $dateFecha){
                    $act = Actividad_Grupal_Usuario::where('usuario_id',$request->usuario_id)->where('actividad_grupal_id', $request->actividad_grupal_id)->first();
                    if ($act->delete()) {
                        $cupo = $this->sumarCupo($actividad->id);
                        $respose = ['msg' => 'Cancelado correctamente', 'Cupos restantes' =>  $cupo];
                        return response()->json($respose, 200);
                    } else {
                        $respose = [
                            'msg' => 'No se pudo cancelar en esta actividad',
                            'data' => $act
                        ];
                        return response()->json($respose, 404);
                    }
                }else{
                    if($dateComp === $dateFecha){
                        if(($actividad->hora_inicial - $date->hour)>4){
                            $act = Actividad_Grupal_Usuario::where('usuario_id',$request->usuario_id)->where('actividad_grupal_id', $request->actividad_grupal_id)->first();
                            if ($act->delete()) {
                                $cupo = $this->sumarCupo($actividad->id);
                                $respose = ['msg' => 'Cancelado correctamente', 'Cupos restantes' =>  $cupo];
                                return response()->json($respose, 200);
                            } else {
                                $respose = [
                                    'msg' => 'No se pudo cancelar en esta actividad',
                                    'data' => $act
                                ];
                                return response()->json($respose, 404);
                            }
                        }else{
                            $respose = [
                                'msj' => 'No puede cancelarlo a 4 horas o menos de tiempo',
                                'data' => $actividad
                            ];
                            return response()->json($respose, 402);
                        }
                    }else{
                        $respose = [
                            'msj' => 'Ya la actividad pasó',
                            'data' => $actividad
                        ];
                        return response()->json($respose, 401);
                    }

                }
            }else{
                $respose = [
                    'msj' => 'Usted no está suscrito a esa actividad',
                    'data' => $value
                ];
                return response()->json($respose, 403);
            }

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
     * Display the specified resource.
     *
     * @param  \App\Actividad_Grupal  $actividad_Grupal
     * @return \Illuminate\Http\Response
     */
    public function show(Actividad_Grupal $actividad_Grupal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Actividad_Grupal  $actividad_Grupal
     * @return \Illuminate\Http\Response
     */
    public function edit(Actividad_Grupal $actividad_Grupal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Actividad_Grupal  $actividad_Grupal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Actividad_Grupal $actividad_Grupal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Actividad_Grupal  $actividad_Grupal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Actividad_Grupal $actividad_Grupal)
    {
        //
    }
}
