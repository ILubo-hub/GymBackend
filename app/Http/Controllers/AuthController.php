<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Usuario;
use App\Historial;
use App\Plan;
use App\Servicio;

class AuthController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        try {
            //Validar entradas

            $this->validate($request, [
                'numero_cedula' => 'required|min:9',
                'nombre' => 'required',
                'apellidos' => 'required',
                'email' => 'required|email|unique:usuarios,email',
                'tipo_usuario_id' => 'required',
                'sexo' => 'required',
                'telefono' => 'required',
                //'fecha_nacimiento' => 'required', Agregar cuando la vista este lista
                'password' => 'required|min:6',
                'plan_id' => 'required'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->responseErrors($e->errors(), 422);
        }

        //Crear objeto a insertar

        $user = new Usuario();
        $user->numero_cedula = $request->numero_cedula;
        $user->nombre = $request->nombre;
        $user->apellidos = $request->apellidos;
        $user->email = $request->email;
        $user->tipo_usuario_id = $request->tipo_usuario_id;
        $user->sexo = $request->sexo;
        $user->telefono = $request->telefono;
        $date = Carbon::now();
        $user->fecha_nacimiento = $date->subYear(20);
        //$user->fecha_inscripcion = $date->format('d/m/Y');
        $user->password = bcrypt($request->password);
        $user->activo = 1;
        $user->save();

        $historial = new Historial();
        $historial->usuario_id = $user->id;
        $historial->plan_id = $request->plan_id;
        $date = Carbon::now();
        $date2 = Carbon::now();
        $historial->fecha_inicio = $date;
        $historial->fecha_final = $date2->addMonth();
        $historial->vigente = 1;

        $plan = new Plan();
        $plan = Plan::where('id', $request->plan_id)->first();
        $plan->servicios()->attach($request->input('servicios_id') === null ? [] : $request->input('servicios_id'));
        $plan = $plan->where('id', $request->plan_id)->first();
        $servicios = new Servicio();
        $servicios = Servicio::where('id', $request->input('servicios_id'))->get();
        $historial->save();
        $user = $user::where('id', $user->id)->with('historiales')->first();

        return response()->json(['Usuario' => $user, 'Plan' => $plan, 'Servicios' => $servicios], 201);
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
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'No autorizado'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => Auth::guard('api')->user(),
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
