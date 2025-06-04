<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
 
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)){
            return response()->json(['error' => 'Credenciales invalidas', 401]);
        }
        return response()->json([
            'token' => $token,
            'user' => auth()->user()
        ]);
    }

    public function register(Request $request)
    {
        $role = Role::where('label', 'user')->first()->id; // Buscamos el id del rol de usuario
        $user= User::create([
        'role_id' => $role,
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password)
        ]);
        return response()->json([
            'message' => 'Usuario creado con éxito',
            'user' => $user
            ]);
        }
    
    public function logout()
        {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Sesión cerrada con éxito']);
        }
    
}
