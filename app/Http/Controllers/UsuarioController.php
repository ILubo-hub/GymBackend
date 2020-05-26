<?php

namespace App\Http\Controllers;

use App\Actividad_Grupal;
use App\Historial;
use App\Pago_Cancelado;
use App\Plan;
use App\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class UsuarioController extends Controller
{
    public function __construct()
    {
        //No se quieren proteger todas las acciones
        //Agregar segundo argumento
        $this->middleware('jwt.auth', ['except' => [
            'index', 'restoreById'
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
            $usuario = Usuario::orderBy('id', 'asc')->with(['historiales', 'tipo_usuario'])->get();
            $respose = [
                'msg' => 'Lista de Usuarios Registrados',
                'data' => $usuario
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

        $usuario = DB::select("SELECT gymdb.historials.*, gymdb.usuarios.*, gymdb.plans.nombre as nombre_plan, gymdb.plans.descripcion as descripcion, gymdb.plans.precio as precio  FROM gymdb.usuarios, gymdb.historials, gymdb.plans  where gymdb.usuarios.id= $id and gymdb.historials.vigente=1 and gymdb.historials.usuario_id = gymdb.usuarios.id and gymdb.historials.plan_id = gymdb.plans.id;", [1]);
        $response = [
            'msj' => 'Historial Activo',
            'data' => $usuario
        ];
        return response()->json($response, 200);
    }

    public function clientesv2()
    {
        try {

            $usuarios = Usuario::where('tipo_usuario_id', 3)->with('historiales')->get();
            $respose = [
                'msg' => 'Lista de clientes Registrados',
                'data' => $usuarios
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function clientv3()
    {

        $actividades = DB::select("SELECT gymdb.usuarios.*, gymdb.plans.id as plan_id FROM gymdb.usuarios, gymdb.historials, gymdb.plans  where gymdb.usuarios.id = gymdb.historials.usuario_id and gymdb.historials.vigente and gymdb.historials.plan_id = gymdb.plans.id", [1]);
        $response = [
            'msj' => 'Actividades según el plan',
            'data' => $actividades
        ];
        return response()->json($response, 200);
    }

    public function clientes()
    {
        try {

            $usuarios = Usuario::where('id', 5000)->get();
            $historiales = Historial::orderBy('fecha_inicio', 'asc')->get();
            $obj = new Historial();
            for ($i = 0; $i < sizeOf($historiales); $i++) {
                $usuario = new Usuario();
                $usuario = Usuario::where('id', $historiales[$i]->usuario_id)->with('historiales')->first();
                $usuarios[$i] = $usuario;
            }
            $respose = [
                'msg' => 'Lista de clientes Registrados',
                'data' => $usuarios
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function UsuariosT()
    {
        try {
            $usersDeleted = Usuario::withTrashed()->get();
            $response = [
                'msg' => 'Lista de usuarios completa',
                'data ' => $usersDeleted
            ];
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function UsuariosBorrados()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            if ($user->tipo_usuario_id === 1) {
                $usuariosBorrados = Usuario::onlyTrashed()->get();
                $response = [
                    'msg' => 'Lista de usuarios eliminados',
                    'Usuarios ' => $usuariosBorrados
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'msg' => 'Necesita permisos de administrador'
                ];
                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function deleteById($idUsuario)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            if ($user->tipo_usuario_id === 1) {
                $usuario = Usuario::find($idUsuario);
                $usuario->delete();
                $response = [
                    'msg' => 'Usuario eliminado temporalmente',
                    'Usuario ' => $usuario
                ];
                return response()->json($response, 200);
            } else {
                $response = [
                    'msg' => 'Necesita permisos de administrador'
                ];
                return response()->json($response, 404);
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
            $usuario = Usuario::where('id', $id)->with('historiales')->first();
            $response = [
                'msg' => 'Usuario Registrado',
                'data' => $usuario
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
     * Display the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function show($filtro)
    {
        try {
            if ($filtro == 'activo') {
                $usuario = Usuario::orderBy('fecha_inscripcion', 'asc')->get();
                $response = [
                    'msg' => 'Lista de Usuarios Registrados',
                    'data' => $usuario
                ];
                return response()->json($response, 200);
            } else {
                if ($filtro == 'borrado') {
                    $usuariosBorrados = Usuario::onlyTrashed()->get();
                    $response = [
                        'msg' => 'Lista de usuarios eliminados',
                        'Usuarios ' => $usuariosBorrados
                    ];
                    return response()->json($response, 200);
                } else {

                    //$historiales = Historial::where('plan_id', $filtro)->get();
                    //$usuarios = Usuario::where('id', $historiales)->get();
                    $usuarios = Usuario::where('id', 5000)->get();
                    $historiales = Historial::where('plan_id', $filtro)->get();
                    $obj = new Historial();
                    for ($i = 0; $i < sizeOf($historiales); $i++) {
                        $usuario = new Usuario();
                        $usuario = Usuario::where('id', $historiales[$i]->usuario_id)->first();
                        if (!$usuario->is_null) {

                            $usuarios[$i] = $usuario;
                        }
                    }
                    $response = [
                        'msg' => 'Lista de Usuarios con el plan ingresado',
                        'data' => $usuarios
                    ];
                    return response()->json($response, 200);
                }
                $response = [
                    'msg' => 'No se han encontrado resultados',
                    'data' => []
                ];
                return response()->json($response, 200);
            }
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'fecha_inicio' => 'required',
                'activo' => 'required|numeric',
                'plan_id' => 'required'
            ]);
            //Obtener el usuario autentificado actual
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        if ($user->tipo_usuario_id === 1) {
            $usuario = Usuario::find($id);
            $usuario->activo = $request->input('activo');

            $historial = Historial::where(['usuario_id' => $id, 'vigente' => 1])->first();
            $date = Carbon::now();
            $date2 = Carbon::now();
            $historial->fecha_inicio = $date->addMonth();
            $historial->fecha_final = $date2->addMonths(2);
            $historial->plan_id = $request->input('plan_id');

            if ($usuario->save()) {
                if ($historial->save()) {
                    $response = [
                        'msg' => 'Información actualizada!',
                        'data' => $id
                    ];
                }

                return response()->json($response, 201);
            } else {
                $response = [
                    'msg' => 'Error durante la actualización'
                ];
                return response()->json($response, 404);
            }
        } else {
            $response = [
                'msg' => 'Requiere permisos de administrador'
            ];
            return response()->json($response, 404);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'nombre' => 'required',
                'apellidos' => 'required',
                // 'email' => 'required|email|unique:usuarios,email',
                'sexo' => 'required',
                'telefono' => 'required',
                'fecha_nacimiento' => 'required',
                'password' => 'required|min:6',
                'plan_id' => 'required'
            ]);
            //Obtener el usuario autentificado actual
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        //Crear objeto a insertar

        $user = Usuario::find($id);
        $user->nombre = $request->input('nombre');
        $user->apellidos = $request->input('apellidos');
        // $user->email = $request->input('email');
        $user->sexo = $request->input('sexo');
        $user->telefono = $request->input('telefono');
        $date = Carbon::now();
        $user->fecha_nacimiento = $date->subYear(20);
        //$user->fecha_inscripcion = $date->format('d/m/Y');
        $user->password = bcrypt($request->input('password'));
        $user->activo = 1;
        $user->save();

        $historial = Historial::where(['usuario_id' => $user->id, 'vigente' => 1])->first();
        $historial->usuario_id = $user->id;
        $historial->plan_id = $request->input('plan_id');
        $date = Carbon::now();
        $date2 = Carbon::now();
        $historial->fecha_inicio = $date->addMonth();
        $historial->fecha_final = $date2->addMonths(2);
        $historial->vigente = 1;

        $plan = new Plan();
        $plan = Plan::where('id', $request->plan_id)->with('servicios')->first();
        if ($historial->save()) {
            $user = $user::where('id', $user->id)->with('historiales')->first();
            $reponse = [
                'msg' => 'Actualizado con éxito', ['Usuario' => $user, 'Plan' => $plan, 'Servicios']
            ];
            return response()->json($reponse, 201);
        } else {
            $reponse = [
                'msg' => 'Error durante la actualización'
            ];
            return response()->json($reponse, 404);
        }
    }

    public function listaPagos()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            $pago = Pago_Cancelado::where('usuario_id', $user->id)->get();
            $respose = [
                'msg' => 'Lista de pagos realizados',
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

    public function listaPagosPendientes()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
            $historial = Historial::where(['usuario_id' => $user->id, 'vigente' => 1]);
            $date = Carbon::now();
            $historial = $historial::where('fecha_final', '<', $date)->get();
            $user = $user::with('historiales')->first();
            $respose = [
                'msg' => 'Lista de pagos pendientes',
                'data' => $user
            ];
            return response()->json($respose, 200);
        } catch (\Exception $e) {
            return response()->json(
                utf8_encode($e->getMessage()),
                422
            );
        }
    }

    public function pagar(Request $request)
    {
        try {
            $this->validate($request, [
                'usuario_id' => 'required',
                'fecha_pago' => 'required',
                'monto' => 'required',
                'usuario_registra_id' => 'required'
            ]);
            //Obtener el usuario autentificado actual
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg' => 'Usuario no encontrado'], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }
        if ($user->tipo_usuario_id === 3) {
            $historial = Historial::where(['usuario_id' => $request->usuario_id, 'vigente' => 1])->first();
            $date2 = Carbon::now();
            $historial->fecha_final = $date2->addMonth();
            $historial->vigente = 1;

            $date = Carbon::Now();
            $pago = new Pago_Cancelado();
            $pago->usuario_id = $request->usuario_id;
            $pago->fecha_pago = $date;
            $pago->monto = $request->monto;
            $pago->usuario_registra_id = 207940151;
            $pago->save();

            if ($historial->save()) {
                $reponse = [
                    'msg' => 'Pago realizado', ['Pago' => $pago]
                ];
                return response()->json($reponse, 201);
            } else {
                $reponse = [
                    'msg' => 'Error durante la actualización'
                ];
                return response()->json($reponse, 404);
            }
        } else {
            $reponse = [
                'msg' => 'No tiene acceso a esta información'
            ];
            return response()->json($reponse, 404);
        }
    }

    public function restoreById($id)
    {
        Usuario::withTrashed()->findOrFail($id)->restore();
        return 'Usuario: ' . $id . ' restaurado';
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usuario $usuario)
    {
        //
    }


    /*public function historial($id)
    {
        $historiales = Historial::where('plan_id', $id)->get();
        $obj = new Historial();
        foreach ($historiales as $obj) {
            $usuario = new Usuario();
            $usuario = Usuario::where('id', $obj->usuario_id)->first();
            if (!$usuario->is_null) {
                $usuarios;
                $usuarios->array_push($usuario);
            }
        }
    }

    public function historial($id)
    {
        $historiales = Historial::where('plan_id', $id)->get();
        $obj = new Historial();
        for ($i = 0; $i < sizeOf($historiales); $i++) {
            $usuario = new Usuario();
            $usuario = Usuario::where('id', $historiales[$i]->usuario_id)->first();
            if (!$usuario->is_null) {
                $usuarios;
                $usuarios[$i]->array_push($usuario);
            }
        }
    }*/
}
