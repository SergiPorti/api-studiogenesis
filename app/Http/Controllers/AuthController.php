<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //TODO: Falta tractament de foto en el model
        //TODO: Creacio de foto en el model
        $request->validate([
            'email' => 'filled|string|email',
            'username' => 'filled|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'username', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->username)->first();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            "data" => [
                'message' => 'S\'ha accedit correctament al compte', 'token' => $token,
                'username' => $user->username,
                'email' => $user->email,
                'lastname' => $user->lastname,
                'birthdate' => $user->birthdate,
                'name' => $user->name
            ]
        ], 200);
    }

    public function loginByToken(Request $request)
    {
        //TODO: Verificacio del USER VIA TOKEN
        
        
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->username)->first();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            "data" => [
                'message' => 'S\'ha accedit correctament al compte', 'token' => $token,
                'username' => $user->username,
                'email' => $user->email,
            ]
        ], 200);
    }

    public function updatePassword(Request $request)
    {
        try {
            $newPassword = $request->get('password');

            auth()->user()->update(['password' => Hash::make($newPassword)]);

            return response()->json(['data' => 'Contrassenya restablerta correctament'], 200);
        } catch (\Exception $e) {
            return response()->json(["error_message" => $e, "message" => "Error al restablir la contrassenya"], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'lastname' => 'required|string',
                'birthdate' => 'required|string',
                'username' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json(['error_message' => $validator->errors(), 'message' => 'Error al registrarse'], 422);
            }
            $validatedData = $request->all();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $user = User::create($validatedData);

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                "data" => [
                    'message' => 'Se ha creado el usuario correctamente',
                    'token' => $token,
                    'username' => $request->get('username'),
                    'email' => $request->get('email')
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error_message' => $e, 'message' => 'Error al crear l\'usuari, el correu electrònic o el nom ja estan en ús'], 500);
        }
    }
}
