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

        $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed'
        ]);

        $role = "2"; 
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
